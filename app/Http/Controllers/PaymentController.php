<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Stripe classes are optional at runtime (require via composer)
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createIntent(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // set stripe key from config/services.php via .env
        $secret = config('services.stripe.secret');
        if (! $secret) {
            // fallback to environment variable if config not available (config cache issues)
            $secret = env('STRIPE_SECRET');
        }
        if (! $secret) {
            return response()->json(['error' => 'Stripe secret not configured. Set STRIPE_SECRET in .env and run php artisan config:clear'], 500);
        }

        if (! class_exists('\Stripe\\Stripe') || ! class_exists('\Stripe\\PaymentIntent')) {
            return response()->json(['error' => 'stripe/stripe-php not installed. Run composer require stripe/stripe-php'], 500);
        }

        try {
            $stripeClass = '\\Stripe\\Stripe';
            $paymentIntentClass = '\\Stripe\\PaymentIntent';

            // runtime call to avoid static analyzer issues when stripe package is not installed
            $stripeClass::setApiKey($secret);

            $amount = intval($order->total * 100); // cents

            $intent = $paymentIntentClass::create([
                'amount' => $amount,
                'currency' => 'idr',
                'metadata' => ['order_id' => $order->id],
            ]);

            return response()->json(['client_secret' => $intent->client_secret]);
        } catch (\Exception $e) {
            Log::error('Stripe createIntent error: '.$e->getMessage());
            return response()->json(['error' => 'failed to create payment intent: '.$e->getMessage()], 500);
        }
    }

    // Stripe webhook endpoint — configure your webhook in Stripe dashboard to call this URL
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');
        if (! $endpoint_secret) {
            $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        }

        if (! $endpoint_secret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'webhook secret not configured - set STRIPE_WEBHOOK_SECRET in .env'], 500);
        }

        if (! class_exists('\\Stripe\\Webhook')) {
            Log::error('Stripe Webhook class not available - stripe/stripe-php not installed');
            return response()->json(['error' => 'stripe/stripe-php not installed'], 500);
        }

        try {
            $webhookClass = '\\Stripe\\Webhook';
            $event = $webhookClass::constructEvent($payload, $sig, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage());
            return response()->json(['error' => 'invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $orderId = $intent->metadata->order_id ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    // create or update transaction
                    $trx = Transaction::firstOrCreate([
                        'order_id' => $order->id,
                    ], [
                        'status' => 'disiapkan',
                        'payment_method' => 'card',
                        'paid_at' => now(),
                    ]);

                    $trx->update(['status' => 'disiapkan', 'paid_at' => now(), 'payment_method' => 'card']);
                    $order->update(['status' => 'disiapkan']);
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}

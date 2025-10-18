Stripe / Payment Integration Notes

NOTE: Stripe integration was removed from the application per the recent request to avoid runtime errors. If you want to re-enable Stripe later, add the keys to `.env`, restore `stripe/stripe-php` in `composer.json` and run `composer require stripe/stripe-php`.

Goal
- Make payment_method a structured value and allow real card payments (Stripe) or offline methods (transfer, cod).

Quick summary for Stripe (recommended)
1) Use stripe-php + Stripe Elements or Laravel Cashier (for subscriptions). For one-off payments, stripe-php is fine.

Server side (example using stripe-php)
- composer require stripe/stripe-php

- env: add STRIPE_SECRET and STRIPE_KEY in .env

Example controller flow (create PaymentIntent):

// use Stripe\Stripe;
// use Stripe\PaymentIntent;

Stripe::setApiKey(config('services.stripe.secret'));
$paymentIntent = PaymentIntent::create([
  'amount' => intval($order->total * 100), // in cents
  'currency' => 'idr',
  'metadata' => ['order_id' => $order->id],
]);

Return client_secret to the frontend and use Stripe.js to confirm card details.

Frontend (Stripe.js)
- Include https://js.stripe.com/v3/
- Initialize Stripe with publishable key
- Use Elements to collect card info and call stripe.confirmCardPayment(client_secret, {payment_method: {card: element}})

On successful payment, call your /transactions/pay/{orderId} endpoint with payment_method="card" to record paid_at and set status to 'disiapkan' or directly mark 'dikirim' depending on your flow.

Notes
- For testing use Stripe test keys and client side test card numbers.
- If you prefer an easier Laravel integration, consider Laravel Cashier which wraps Stripe APIs.

Security
- Always use HTTPS for card collection.
- Do not send secret keys to the frontend.

If you want, I can:
- add a simple PaymentIntent endpoint and small Stripe JS snippet in the checkout page, or
- implement a server-side flow that marks payment_method='card' and expects webhook confirmation.

Setup steps (exact)
1) Install stripe-php in project:

```bash
composer require stripe/stripe-php
```

2) Add keys to `.env` (use test keys during development):

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

3) Add to `config/services.php`:

```php
'stripe' => [
  'key' => env('STRIPE_KEY'),
  'secret' => env('STRIPE_SECRET'),
  'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

4) Expose webhook URL and register it in Stripe dashboard (for local dev use a tunnel like ngrok and set the webhook secret in .env). The webhook route in this project is `/webhook/stripe`.

Implementation note
- In the quick implementation I added `PaymentController::createIntent` and a basic webhook handler that listens to `payment_intent.succeeded` and marks the corresponding `Order`/`Transaction` as paid (status 'disiapkan').
- The checkout JS in `resources/views/cart/index.blade.php` currently has a placeholder method to get the created order id (it assumes order id 1). For production, add a small API that returns the just-created order id after `/cart/checkout` runs (e.g., respond with JSON containing the order id rather than redirect), then use that id to create the PaymentIntent.

If you want, I can implement that API next so the Stripe flow is end-to-end without assumptions.


<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Allow login by email or username
        $loginInput = $request->input('email');
        $password = $request->input('password');
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $loginInput, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Redirect admin to dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // If user was trying to access admin area, redirect to home
            if ($request->is('admin/*')) {
                return redirect('/');
            }
            
            // For normal users, redirect to intended URL or home
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email/Username atau password salah.',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return view('auth.registrasi');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->no_hp,
            'address' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // role must match enum defined in migration: ['admin','user']
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

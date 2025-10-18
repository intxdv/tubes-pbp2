<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Form registrasi
    public function create()
    {
        return view('auth.register');
    }

    // Proses registrasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect('/login');
    }

    // Form login
    public function loginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
{
    $login = $request->input('login');
    $password = $request->input('password');
    $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if (Auth::attempt([$field => $login, 'password' => $password])) {
        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/dashboard');
        }
    }

    return redirect()->back()->withInput()->withErrors([
        'login' => 'Username/email atau password salah',
    ]);
}


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

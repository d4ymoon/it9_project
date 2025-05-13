<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Try login with email
        if (Auth::attempt(['email' => $login, 'password' => $password])) {
            $request->session()->regenerate();
            
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Please login with an admin account.',
                ]);
            }
            
            // Always redirect to dashboard after successful login
            return redirect('/dashboard');
        }

        // Try login with employee ID
        $user = User::whereHas('employee', function ($query) use ($login) {
            $query->where('id', $login);
        })->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            
            // Check if user is admin
            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Please login with an admin account.',
                ]);
            }
            
            // Always redirect to dashboard after successful login
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}


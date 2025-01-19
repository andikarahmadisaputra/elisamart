<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->input('identifier');
        $password = $request->input('password');

        // Tentukan field berdasarkan format identifier
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) 
            ? 'email' 
            : (strlen($identifier) === 16 && ctype_digit($identifier) 
                ? 'nik' 
                : 'nia');

        // Validasi format NIA
        if ($field === 'nia' && !preg_match('/^\d+(\.\d+)*$/', $identifier)) {
            return back()->withErrors(['identifier' => 'Format NIA tidak valid.'])->withInput();
        }

        // Autentikasi berdasarkan field yang ditemukan
        if (Auth::attempt([$field => $identifier, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors(['identifier' => 'Identitas atau kata sandi salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

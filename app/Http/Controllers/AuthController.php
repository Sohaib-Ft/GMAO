<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    // Afficher le formulaire de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Traitement du login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Authentification: find user and verify password manually to avoid
        // BcryptHasher exceptions when stored hashes use another algorithm.
        $user = User::where('email', $credentials['email'])->first();

        if ($user && password_verify($credentials['password'], $user->password)) {
            // Check if user is active
            if (!$user->is_active) {
                return back()->withErrors([
                    'email' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
                ]);
            }

            // Optionnel: ré-hasher le mot de passe avec le driver actuel
            try {
                if (\Illuminate\Support\Facades\Hash::needsRehash($user->password)) {
                    $user->password = Hash::make($credentials['password']);
                    $user->save();                 }
            } catch (\Throwable $e) {
                // Si Hash::needsRehash déclenche, on l'ignore et on continue
            }

            // Ensure no persistent "remember me" cookie keeps the user logged in
            // Clear any existing remember token cookie and do a normal login (no remember)
            try {
                Cookie::queue(Cookie::forget(Auth::getRecallerName()));
            } catch (\Throwable $e) {
                // ignore if recaller name not available
            }

            Auth::login($user, false);
            $request->session()->regenerate();

            // Redirection selon le rôle
            if ($user->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } elseif ($user->role === 'technicien') {
                return redirect()->route('dashboard.technicien');
            } else {
                return redirect()->route('dashboard.employe');
            }
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect',
        ]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        // Clear remember_token in database to avoid persistent login
        if (Auth::check()) {
            $u = Auth::user();
            $u->remember_token = null;
            $u->save();
        }

        // Forget the recaller cookie if present
        try {
            Cookie::queue(Cookie::forget(Auth::getRecallerName()));
        } catch (\Throwable $e) {
            // ignore
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

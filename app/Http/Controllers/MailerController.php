<?php

namespace App\Http\Controllers;

use App\Mail\TemporaryPasswordMail;
use App\Mail\UserActivated;
use App\Mail\UserDeactivated;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailerController extends Controller
{
    /**
     * Envoi email avec mot de passe temporaire
     */
    public function sendWelcomeTempPassword(User $user): void
    {
        try {
            // 🔐 Génération UNIQUE
            $temporaryPassword = Str::random(12);

            // 🔒 Hash + sauvegarde
            $user->update([
                'password' => Hash::make($temporaryPassword),
                'force_password_change' => true,
            ]);

            // 📧 Envoi email (SAME password)
            Mail::to($user->email)
                ->send(new TemporaryPasswordMail($user, $temporaryPassword));

        } catch (\Throwable $e) {
            Log::error(
                "Erreur email pwd temporaire (user ID {$user->id}) : {$e->getMessage()}"
            );
        }
    }

    /**
     * Envoi email activation / désactivation
     */
    public function sendUserStatusChanged(User $user): void
    {
        try {
            if ($user->is_active) {
                Mail::to($user->email)->send(new UserActivated($user));
            } else {
                Mail::to($user->email)->send(new UserDeactivated($user));
            }

        } catch (\Throwable $e) {
            Log::error(
                "Erreur email statut user (user ID {$user->id}) : {$e->getMessage()}"
            );
        }
    }
}
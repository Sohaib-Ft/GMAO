<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeTempPassword;
use App\Mail\UserStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class MailerController extends Controller
{
    public function sendWelcomeTempPassword(User $user, string $password): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeTempPassword($user, $password));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erreur d'envoi d'email de bienvenue : " . $e->getMessage());
        }
    }

    public function sendUserStatusChanged(User $user, string $status): void
    {
        try {
            Mail::to($user->email)->send(new UserStatusChanged($user, $status));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erreur d'envoi d'email de changement de statut : " . $e->getMessage());
        }
    }
}

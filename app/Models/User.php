<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WorkOrder;
use App\Models\Reclamation;
use App\Models\Message;
use App\Models\Intervention;
use App\Models\Notification;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;
    
    /**
     * Obtenir l'URL de la photo de profil.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Envoyez la notification de réinitialisation de mot de passe.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'profile_photo_path'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relations
    public function employeWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'employe_id');
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class, 'employe_id');
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function technicienWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'technicien_id');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'technicien_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

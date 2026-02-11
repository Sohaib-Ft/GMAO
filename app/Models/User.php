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
        'name', 'email', 'password', 'role', 'is_active'
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

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technicien' || $this->role === 'technician';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employe';
    }
}

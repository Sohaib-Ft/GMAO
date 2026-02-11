<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $guarded = [];

    /**
     * L'utilisateur concerné par la demande (si trouvé par email)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * L'administrateur qui a traité la demande
     */
    public function processingAdmin()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

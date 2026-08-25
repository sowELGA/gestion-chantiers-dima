<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeResetMdp extends Model
{
    protected $table = 'demandes_reset_mdp';

    protected $fillable = [
        'email',
        'statut',
        'traite_le',
    ];

    protected $casts = [
        'traite_le' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nomUser',
        'prenomUser',
        'email',
        'password',
        'role',
        'premiere_connexion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'premiere_connexion' => 'boolean',
        'password'           => 'hashed',
    ];

    // Accesseur nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenomUser . ' ' . $this->nomUser;
    }

    // Scopes
    public function scopeDirection($query)
    {
        return $query->where('role', 'direction');
    }

    public function scopeChefProjet($query)
    {
        return $query->where('role', 'chef_projet');
    }

    public function scopePointeur($query)
    {
        return $query->where('role', 'pointeur');
    }

    // Relations
    public function chantiersGeres()
    {
        return $this->hasMany(Chantier::class, 'chef_projet_id');
    }

    public function chantiersPointes()
    {
        return $this->hasMany(Chantier::class, 'pointeur_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}

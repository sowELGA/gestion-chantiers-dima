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
        'telUser',
        'password',
        'role',
        'premiere_connexion',
        'actif',
        'est_super_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'premiere_connexion' => 'boolean',
        'actif' => 'boolean',
        'password'           => 'hashed',
        'est_super_admin' => 'boolean'
    ];

    // Accesseur nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenomUser . ' ' . $this->nomUser;
    }

    // Scopes

    // Scope actifs
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    // Scope inactifs
    public function scopeInactif($query)
    {
        return $query->where('actif', false);
    }

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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function aLaPermission(string $code): bool
    {
        if ($this->est_super_admin) {
            return true;
        }

        if ($this->role !== 'direction') {
            return false;
        }

        return $this->relationLoaded('permissions')
            ? $this->permissions->contains('code', $code)
            : $this->permissions()->where('code', $code)->exists();
    }
}

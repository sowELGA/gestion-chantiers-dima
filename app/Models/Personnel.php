<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnels';

    protected $fillable = [
        'nomPersonnel',
        'prenomPersonnel',
        'statutPersonnel',
        'poste_id',
        'chantier_id',
    ];

    // Accesseur nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenomPersonnel . ' ' . $this->nomPersonnel;
    }

    // Relations
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class, 'ouvrier_id');
    }

    public function recapsHebdomadaires()
    {
        return $this->hasMany(RecapHebdomadaire::class, 'ouvrier_id');
    }
}

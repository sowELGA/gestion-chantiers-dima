<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    protected $table = 'postes';

    protected $fillable = [
        'libelle',
    ];

    public function getMetierBaseAttribute(): string
    {
        return ucfirst(trim(preg_replace('/^(Chef|Membre)\s+(de\s+|d\'|)?/i', '', $this->libelle)));
    }

    public function personnel()
    {
        return $this->hasMany(Ouvrier::class, 'poste_id');
    }

    public function tauxSalaires()
    {
        return $this->hasMany(TauxSalaire::class, 'poste_id');
    }
}

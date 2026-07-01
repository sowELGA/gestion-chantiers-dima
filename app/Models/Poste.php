<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    protected $table = 'postes';

    protected $fillable = [
        'libelle',
    ];

    public function personnel()
    {
        return $this->hasMany(Personnel::class, 'poste_id');
    }

    public function tauxSalaires()
    {
        return $this->hasMany(TauxSalaire::class, 'poste_id');
    }
}

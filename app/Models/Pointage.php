<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $table = 'pointages';

    protected $fillable = [
        'date',
        'statutPointage',
        'heures_sup',
        'ouvrier_id',
        'chantier_id',
    ];

    protected $casts = [
        'date'       => 'date',
        'heures_sup' => 'decimal:1',
    ];

    public function ouvrier()
    {
        return $this->belongsTo(Personnel::class, 'ouvrier_id');
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }
}

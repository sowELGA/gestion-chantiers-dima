<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecapHebdomadaire extends Model
{
    use HasFactory;

    protected $table = 'recaps_hebdomadaires';

    protected $fillable = [
        'semaine',
        'annee',
        'jours_presents',
        'total_heures_sup',
        'salaire_base',
        'salaire_heures_sup',
        'salaire_total',
        'statut',
        'valide_le',
        'ouvrier_id',
        'chantier_id',
        'soumis_par_id',
        'valide_par_id',
    ];

    protected $casts = [
        'valide_le'          => 'datetime',
        'salaire_base'       => 'decimal:2',
        'salaire_heures_sup' => 'decimal:2',
        'salaire_total'      => 'decimal:2',
        'total_heures_sup'   => 'decimal:1',
    ];

    // Relations
    public function ouvrier()
    {
        return $this->belongsTo(Personnel::class, 'ouvrier_id');
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function soumisParUser()
    {
        return $this->belongsTo(User::class, 'soumis_par_id');
    }

    public function valideParUser()
    {
        return $this->belongsTo(User::class, 'valide_par_id');
    }
}

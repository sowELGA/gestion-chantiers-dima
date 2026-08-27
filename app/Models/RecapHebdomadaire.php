<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecapHebdomadaire extends Model
{
    protected $table = 'recaps_hebdomadaires';

    protected $fillable = [
        'semaine',
        'annee',
        'jours_presents',
        'total_heures_sup',
        'statutRecap',
        'motif_rejet',
        'valide_le',
        'ouvrier_id',
        'chantier_id',
        'soumis_par_id',
        'valide_par_id',
    ];

    protected $casts = [
        'valide_le'        => 'datetime',
        'total_heures_sup' => 'decimal:1',
    ];

    public function ouvrier()
    {
        return $this->belongsTo(Ouvrier::class, 'ouvrier_id');
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

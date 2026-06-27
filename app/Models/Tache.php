<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomTache',
        'type',
        'descriptionTache',
        'besoins_materiels',
        'besoins_materiaux',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'avancement',
        'statutTache',
        'sous_traitant',
        'est_en_retard',
        'chantier_id',
        'phase_id',
        'tache_precedente_id',
        'responsable_id',
    ];

    protected $casts = [
        'date_debut_prevue' => 'date',
        'date_fin_prevue'   => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle'   => 'date',
        'est_en_retard'     => 'boolean',
    ];

    // Relations
    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function tachePrecedente()
    {
        return $this->belongsTo(Tache::class, 'tache_precedente_id');
    }
}

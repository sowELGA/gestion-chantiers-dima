<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    protected $table = 'phases';

    protected $fillable = [
        'nomPhase',
        'ordre',
        'date_debut',
        'date_fin_prevue',
        'avancement',
        'statutPhase',
        'chantier_id',
    ];

    protected $casts = [
        'date_debut'      => 'date',
        'date_fin_prevue' => 'date',
    ];

    // Accesseur avancement calculé depuis les tâches
    public function getAvancementCalculeAttribute(): float
    {
        $taches = $this->taches;
        if ($taches->isEmpty()) return 0;
        return round($taches->avg('avancement'), 1);
    }

    // Relations
    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'phase_id')
            ->orderBy('date_debut_prevue');
    }
}

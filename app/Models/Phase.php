<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

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

    // Accesseurs
    public function getAvancementAttribute(): float
    {
        $taches = $this->taches;
        if ($taches->isEmpty()) return 0;
        return round($taches->avg('avancement'), 2);
    }

    // Relations
    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'phase_id');
    }
}

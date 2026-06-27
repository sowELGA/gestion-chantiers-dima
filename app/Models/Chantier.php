<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomChantier',
        'adresse',
        'budget_prevu',
        'budget_consomme',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'statut',
        'chef_projet_id',
        'pointeur_id',
    ];

    protected $casts = [
        'date_debut'      => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'budget_prevu'    => 'decimal:2',
        'budget_consomme' => 'decimal:2',
    ];

    // Accesseurs
    public function getBudgetRestantAttribute(): float
    {
        return $this->budget_prevu - $this->budget_consomme;
    }

    public function getPourcentageBudgetAttribute(): float
    {
        if ($this->budget_prevu == 0) return 0;
        return round(($this->budget_consomme / $this->budget_prevu) * 100, 2);
    }

    public function getAvancementGlobalAttribute(): float
    {
        $taches = $this->taches;
        if ($taches->isEmpty()) return 0;
        return round($taches->avg('avancement'), 2);
    }

    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_fin_prevue < now() && $this->statut !== 'livre';
    }

    // Relations
    public function chefProjet()
    {
        return $this->belongsTo(User::class, 'chef_projet_id');
    }

    public function pointeur()
    {
        return $this->belongsTo(User::class, 'pointeur_id');
    }

    public function phases()
    {
        return $this->hasMany(Phase::class, 'chantier_id')
            ->orderBy('ordre');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'chantier_id');
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class, 'chantier_id');
    }

    public function tauxSalaires()
    {
        return $this->hasMany(TauxSalaire::class, 'chantier_id');
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class, 'chantier_id');
    }

    public function recapsHebdomadaires()
    {
        return $this->hasMany(RecapHebdomadaire::class, 'chantier_id');
    }

    public function approvisionnements()
    {
        return $this->hasMany(Approvisionnement::class, 'chantier_id');
    }

    public function rapportsEntrees()
    {
        return $this->hasMany(RapportsEntree::class, 'chantier_id');
    }

    public function depenses()
    {
        return $this->hasMany(DepensesChantier::class, 'chantier_id');
    }

    public function rapports()
    {
        return $this->hasMany(RapportsChantier::class, 'chantier_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'chantier_id');
    }
}

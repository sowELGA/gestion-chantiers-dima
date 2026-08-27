<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomChantier',
        'localisation',
        'budget_prevu',
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
    ];

    // ══════════════════════════════════════════════════════════
    // ACCESSEURS
    // ══════════════════════════════════════════════════════════

    // budget_consomme n'est plus une colonne : c'est la somme des
    // dépenses liées au chantier, calculée à la volée. Si la relation
    // "depenses" est déjà chargée (eager loading), on réutilise la
    // collection en mémoire pour éviter une requête supplémentaire.
    public function getBudgetConsommeAttribute(): float
    {
        if ($this->relationLoaded('depenses')) {
            return (float) $this->depenses->sum('montant');
        }

        return (float) $this->depenses()->sum('montant');
    }

    public function getBudgetRestantAttribute(): float
    {
        return $this->budget_prevu - $this->budget_consomme;
    }

    public function getPourcentageBudgetAttribute(): float
    {
        if ($this->budget_prevu == 0) return 0;
        return round(($this->budget_consomme / $this->budget_prevu) * 100, 2);
    }

    // Avancement global du chantier = moyenne de l'avancement de toutes
    // ses tâches. Réutilise la collection "taches" si elle est déjà
    // chargée, pour rester performant sur la liste des chantiers.
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

    // ══════════════════════════════════════════════════════════
    // RELATIONS
    // ══════════════════════════════════════════════════════════

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
        return $this->hasMany(Ouvrier::class, 'chantier_id');
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
        return $this->hasMany(RapportChantier::class, 'chantier_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'chantier_id');
    }
}

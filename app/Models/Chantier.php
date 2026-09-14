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

    /**
     * Le budget prévu est désormais optionnel : si aucun budget n'a été
     * défini, "il reste combien" n'a pas de sens — on renvoie null plutôt
     * qu'un nombre négatif trompeur (0 - dépenses).
     */
    public function getBudgetRestantAttribute(): ?float
    {
        if ($this->budget_prevu === null) {
            return null;
        }

        return (float) $this->budget_prevu - $this->budget_consomme;
    }

    /**
     * Idem : sans budget défini, un pourcentage n'a pas de sens (ni 0%,
     * qui laisserait croire que le budget est tenu, ni une division par
     * zéro). On renvoie null, à charge des vues d'afficher "—" ou
     * "Non défini" dans ce cas.
     */
    public function getPourcentageBudgetAttribute(): ?float
    {
        if ($this->budget_prevu === null || (float) $this->budget_prevu == 0) {
            return null;
        }

        return round(($this->budget_consomme / $this->budget_prevu) * 100, 2);
    }

    /**
     * Avancement global du chantier = moyenne de l'avancement de toutes
     * ses tâches. Réutilise la collection "taches" si elle est déjà
     * chargée (comme pour budget_consomme) ; sinon, calcule la moyenne
     * directement en base plutôt que de rapatrier toutes les lignes pour
     * les moyenner en PHP.
     */
    public function getAvancementGlobalAttribute(): float
    {
        if ($this->relationLoaded('taches')) {
            $taches = $this->taches;
            return $taches->isEmpty() ? 0 : round($taches->avg('avancement'), 2);
        }

        $moyenne = $this->taches()->avg('avancement');

        return $moyenne !== null ? round((float) $moyenne, 2) : 0;
    }

    /**
     * Un chantier n'est considéré en retard qu'à partir du lendemain de
     * sa date de fin prévue. date_fin_prevue est castée en date pure
     * (minuit) : la comparer directement à now() (l'instant précis) le
     * ferait apparaître "en retard" dès 00h00 le jour même de l'échéance,
     * alors que la journée n'est pas encore terminée.
     */
    public function getEstEnRetardAttribute(): bool
    {
        return $this->date_fin_prevue->lt(today()) && $this->statut !== 'livre';
    }

    /**
     * Source unique de vérité pour les transitions de statut autorisées,
     * utilisée à la fois par ChantierService::verifierTransitionAutorisee()
     * (contrôle serveur) et par la vue show.blade.php (menu "Changer le
     * statut") — pour éviter que les deux se désynchronisent si la règle
     * métier évolue un jour.
     */
    public const TRANSITIONS = [
        'en_attente' => ['en_cours' => 'Démarrer le chantier'],
        'en_cours'   => ['suspendu' => 'Suspendre', 'livre' => 'Marquer comme livré'],
        'suspendu'   => ['en_cours' => 'Reprendre le chantier'],
        'livre'      => [],
    ];

    /**
     * Transitions possibles depuis le statut actuel de CE chantier, sous
     * forme [nouveau_statut => libellé affichable].
     */
    public function transitionsDisponibles(): array
    {
        return self::TRANSITIONS[$this->statut] ?? [];
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
        return $this->hasMany(RapportEntree::class, 'chantier_id');
    }

    public function depenses()
    {
        return $this->hasMany(DepensesChantier::class, 'chantier_id');
    }

    public function rapports()
    {
        return $this->hasMany(RapportChantier::class, 'chantier_id');
    }

    public function affectations()
    {
        return $this->hasMany(UserChantier::class);
    }

    public function historiqueChefsProjets()
    {
        return $this->affectations()
            ->whereHas('user', fn($q) => $q->where('role', 'chef_projet'))
            ->with('user')
            ->orderByDesc('debut_affectation');
    }

    public function historiquePointeurs()
    {
        return $this->affectations()
            ->whereHas('user', fn($q) => $q->where('role', 'pointeur'))
            ->with('user')
            ->orderByDesc('debut_affectation');
    }
}

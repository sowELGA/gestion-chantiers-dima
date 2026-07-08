<?php

namespace App\Services;

use App\Models\Personnel;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use App\Models\TauxSalaire;
use Carbon\Carbon;

class PointageService
{
    // ══════════════════════════════════════════════════════════
    // FICHE JOURNALIÈRE
    // ══════════════════════════════════════════════════════════

    public function getFicheDuJour(int $chantierId): array
    {
        $today = Carbon::today();

        $personnel = Personnel::with('poste')
            ->where('chantier_id', $chantierId)
            ->actif()
            ->orderBy('nomPersonnel')
            ->get()
            ->groupBy(fn($p) => $p->poste->libelle);

        $pointagesAujourdhui = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return [
            'date'        => $today,
            'personnel'   => $personnel,
            'pointages'   => $pointagesAujourdhui,
            'ficheExiste' => $pointagesAujourdhui->isNotEmpty(),
        ];
    }

    // Enregistrer fiche journalière complète (présences + heures sup en une fois)
    public function enregistrerFiche(array $data, int $chantierId): void
    {
        if (!$this->semaineModifiable($chantierId)) {
            throw new \Exception(
                'La fiche hebdomadaire a déjà été soumise. Modification impossible.'
            );
        }

        $today = Carbon::today()->toDateString();

        foreach ($data['pointages'] as $ligne) {
            Pointage::updateOrCreate(
                [
                    'ouvrier_id'  => $ligne['ouvrier_id'],
                    'chantier_id' => $chantierId,
                    'date'        => $today,
                ],
                [
                    'statutPointage' => $ligne['statutPointage'],
                    'heures_sup'     => $ligne['statutPointage'] === 'present'
                        ? (float)($ligne['heures_sup'] ?? 0)
                        : 0,
                ]
            );
        }

        // Recalcul automatique du récap de la semaine
        $this->recalculerRecapSemaine($chantierId);
    }

    // ══════════════════════════════════════════════════════════
    // RÉCAP HEBDOMADAIRE
    // ══════════════════════════════════════════════════════════

    // Vérifier si la semaine est modifiable par le pointeur
    public function semaineModifiable(int $chantierId): bool
    {
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if ($recaps->isEmpty()) return true;

        return $recaps->every(
            fn($r) => in_array($r->statut, ['en_attente', 'rejetee'])
        );
    }

    // Récap semaine en cours pour le pointeur (tableau jour par jour)
    public function getRecapSemaineEnCours(int $chantierId): array
    {
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        return $this->getRecapSemaine($chantierId, $semaine, $annee);
    }

    // Récap d'une semaine donnée (utilisé par tous les rôles)
    public function getRecapSemaine(int $chantierId, int $semaine, int $annee): array
    {
        $debut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $fin   = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();

        // Jours de la semaine (lundi à samedi, on exclut dimanche)
        $jours = [];
        for ($i = 0; $i <= 5; $i++) {
            $jours[] = $debut->copy()->addDays($i);
        }

        $personnel = Personnel::with('poste')
            ->where('chantier_id', $chantierId)
            ->actif()
            ->orderBy('nomPersonnel')
            ->get();

        // Tous les pointages de la semaine indexés par ouvrier + date
        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
            ->get()
            ->groupBy('ouvrier_id');

        // Récaps hebdo de la semaine
        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get()
            ->keyBy('ouvrier_id');

        // Statut global de la semaine
        $statutGlobal = $recaps->first()?->statut ?? 'en_attente';
        $motifRejet   = $recaps->first()?->motif_rejet;
        $soumisParId  = $recaps->first()?->soumis_par_id;

        // Construire le tableau ligne par ligne
        $lignes = $personnel->map(function ($ouvrier) use ($pointages, $jours, $recaps) {
            $pointagesOuvrier = $pointages->get($ouvrier->id, collect())
                ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

            $recap = $recaps->get($ouvrier->id);

            $joursDetails = collect($jours)->map(function ($jour) use ($pointagesOuvrier) {
                $dateStr  = $jour->toDateString();
                $pointage = $pointagesOuvrier->get($dateStr);
                return [
                    'date'   => $jour,
                    'statut' => $pointage?->statutPointage ?? null,
                    'h_sup'  => $pointage?->heures_sup ?? 0,
                ];
            });

            return [
                'ouvrier'       => $ouvrier,
                'jours'         => $joursDetails,
                'jours_present' => $joursDetails->where('statut', 'present')->count(),
                'total_h_sup'   => $joursDetails->sum('h_sup'),
                'salaire_base'  => $recap?->salaire_base ?? 0,
                'salaire_h_sup' => $recap?->salaire_heures_sup ?? 0,
                'salaire_total' => $recap?->salaire_total ?? 0,
            ];
        });

        return [
            'semaine'       => $semaine,
            'annee'         => $annee,
            'debut'         => $debut,
            'fin'           => $fin,
            'jours'         => $jours,
            'lignes'        => $lignes,
            'statut'        => $statutGlobal,
            'motif_rejet'   => $motifRejet,
            'soumis_par_id' => $soumisParId,
            'total_general' => $recaps->sum('salaire_total'),
        ];
    }

    // Modifier les pointages d'un jour spécifique (depuis le récap en cas de rejet)
    public function modifierPointageJour(
        int $chantierId,
        string $date,
        array $lignes
    ): void {
        // Vérifier que la semaine est modifiable (statut en_attente ou rejetee)
        $semaine = Carbon::parse($date)->isoWeek();
        $annee   = Carbon::parse($date)->year;

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if (
            $recaps->isNotEmpty() && !$recaps->every(
                fn($r) => $r->statut === 'rejetee'
            )
        ) {
            throw new \Exception(
                'Ce récap ne peut plus être modifié.'
            );
        }

        foreach ($lignes as $ligne) {
            Pointage::updateOrCreate(
                [
                    'ouvrier_id'  => $ligne['ouvrier_id'],
                    'chantier_id' => $chantierId,
                    'date'        => $date,
                ],
                [
                    'statutPointage' => $ligne['statutPointage'],
                    'heures_sup'     => $ligne['statutPointage'] === 'present'
                        ? (float)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        // Recalcul automatique du récap
        $this->recalculerRecapDepuisSemaine($chantierId, $semaine, $annee);
    }

    // Recalcul depuis une semaine/annee spécifique (pas forcément la semaine en cours)
    private function recalculerRecapDepuisSemaine(
        int $chantierId,
        int $semaine,
        int $annee
    ): void {
        $debut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $fin   = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();

        $personnel = Personnel::where('chantier_id', $chantierId)->actif()->get();

        foreach ($personnel as $ouvrier) {
            $pointages = Pointage::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
                ->get();

            $joursPresents  = $pointages->where('statutPointage', 'present')->count();
            $totalHeuresSup = $pointages->sum('heures_sup');

            $taux = TauxSalaire::where('poste_id', $ouvrier->poste_id)
                ->where('chantier_id', $chantierId)
                ->first();

            $salaireBase      = $taux ? $joursPresents * $taux->taux_journalier : 0;
            $salaireHeuresSup = $taux ? $totalHeuresSup * $taux->taux_heure_sup : 0;
            $salaireTotal     = $salaireBase + $salaireHeuresSup;

            $recap = RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->first();

            if ($recap && in_array($recap->statut, ['en_attente', 'rejetee'])) {
                $recap->update([
                    'jours_presents'     => $joursPresents,
                    'total_heures_sup'   => $totalHeuresSup,
                    'salaire_base'       => $salaireBase,
                    'salaire_heures_sup' => $salaireHeuresSup,
                    'salaire_total'      => $salaireTotal,
                ]);
            }
        }
    }

    // Soumettre le récap au chef de projet
    public function soumettreSemaine(int $chantierId, int $pointeurId): void
    {
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        // Recalcul final avant soumission
        $this->recalculerRecapSemaine($chantierId);

        RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['en_attente', 'rejetee'])
            ->update([
                'statut'        => 'soumise',
                'soumis_par_id' => $pointeurId,
                'motif_rejet'   => null,
            ]);
    }

    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET — Validation
    // ══════════════════════════════════════════════════════════

    public function validerSemaine(
        int $chantierId,
        int $semaine,
        int $annee,
        int $chefId
    ): void {
        RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'soumise')
            ->update([
                'statut'        => 'validee_cp',
                'valide_par_id' => $chefId,
                'valide_le'     => now(),
                'motif_rejet'   => null,
            ]);
    }

    public function rejeterSemaine(
        int $chantierId,
        int $semaine,
        int $annee,
        int $chefId,
        string $motif
    ): void {
        RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'soumise')
            ->update([
                'statut'        => 'rejetee',
                'motif_rejet'   => $motif,
                'valide_par_id' => $chefId,
                'valide_le'     => now(),
            ]);
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — Calcul des salaires
    // ══════════════════════════════════════════════════════════

    public function calculerSalaires(int $chantierId, int $semaine, int $annee): void
    {
        $debut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $fin   = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();

        $personnel = Personnel::where('chantier_id', $chantierId)->actif()->get();

        foreach ($personnel as $ouvrier) {
            $pointages = Pointage::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
                ->get();

            $joursPresents  = $pointages->where('statutPointage', 'present')->count();
            $totalHeuresSup = $pointages->sum('heures_sup');

            $taux = TauxSalaire::where('poste_id', $ouvrier->poste_id)
                ->where('chantier_id', $chantierId)
                ->first();

            $salaireBase      = $taux ? $joursPresents * $taux->taux_journalier : 0;
            $salaireHeuresSup = $taux ? $totalHeuresSup * $taux->taux_heure_sup : 0;
            $salaireTotal     = $salaireBase + $salaireHeuresSup;

            RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->update([
                    'jours_presents'     => $joursPresents,
                    'total_heures_sup'   => $totalHeuresSup,
                    'salaire_base'       => $salaireBase,
                    'salaire_heures_sup' => $salaireHeuresSup,
                    'salaire_total'      => $salaireTotal,
                    'statut'             => 'envoyee_direction',
                ]);
        }
    }

    // ══════════════════════════════════════════════════════════
    // TEMPS RÉEL
    // ══════════════════════════════════════════════════════════

    public function getPointagesJourTempsReel(int $chantierId): \Illuminate\Support\Collection
    {
        $today = Carbon::today();

        $personnel = Personnel::with('poste')
            ->where('chantier_id', $chantierId)
            ->actif()
            ->orderBy('nomPersonnel')
            ->get();

        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return $personnel->map(fn($p) => [
            'ouvrier'    => $p,
            'statut'     => $pointages->get($p->idPersonnel)?->statutPointage ?? 'non_pointe',
            'heures_sup' => $pointages->get($p->idPersonnel)?->heures_sup ?? 0,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // CALCUL INTERNE
    // ══════════════════════════════════════════════════════════

    // Recalcul auto du récap après chaque enregistrement journalier
    private function recalculerRecapSemaine(int $chantierId): void
    {
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;
        $debut   = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $fin     = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();

        $personnel = Personnel::where('chantier_id', $chantierId)->actif()->get();

        foreach ($personnel as $ouvrier) {
            $pointages = Pointage::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
                ->get();

            $joursPresents  = $pointages->where('statutPointage', 'present')->count();
            $totalHeuresSup = $pointages->sum('heures_sup');

            $taux = TauxSalaire::where('poste_id', $ouvrier->poste_id)
                ->where('chantier_id', $chantierId)
                ->first();

            $salaireBase      = $taux ? $joursPresents * $taux->taux_journalier : 0;
            $salaireHeuresSup = $taux ? $totalHeuresSup * $taux->taux_heure_sup : 0;
            $salaireTotal     = $salaireBase + $salaireHeuresSup;

            // updateOrCreate uniquement si statut en_attente ou rejetee
            $recap = RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->first();

            if (!$recap || in_array($recap->statut, ['en_attente', 'rejetee'])) {
                RecapHebdomadaire::updateOrCreate(
                    [
                        'ouvrier_id'  => $ouvrier->id,
                        'chantier_id' => $chantierId,
                        'semaine'     => $semaine,
                        'annee'       => $annee,
                    ],
                    [
                        'jours_presents'     => $joursPresents,
                        'total_heures_sup'   => $totalHeuresSup,
                        'salaire_base'       => $salaireBase,
                        'salaire_heures_sup' => $salaireHeuresSup,
                        'salaire_total'      => $salaireTotal,
                        'statut'             => $recap?->statut ?? 'en_attente',
                    ]
                );
            }
        }
    }
}

<?php

namespace App\Services;

use App\Helpers\SemaineHelper;
use App\Models\Personnel;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use App\Models\RecapsHebdomadaire;
use App\Models\TauxSalaire;
use Carbon\Carbon;

class PointageService
{
    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════

    private function personnelActif(int $chantierId): \Illuminate\Support\Collection
    {
        return Personnel::with('poste')
            ->join('postes', 'personnels.poste_id', '=', 'postes.id')
            ->where('personnels.chantier_id', $chantierId)
            ->where('personnels.statutPersonnel', 'actif')
            ->orderByRaw("
        TRIM(REPLACE(LOWER(postes.libelle), 'chef ', ''))
    ")
            ->orderByRaw("
        CASE
            WHEN LOWER(postes.libelle) LIKE 'chef %' THEN 0
            ELSE 1
        END
    ")
            ->orderBy('personnels.nomPersonnel')
            ->select('personnels.*')
            ->get();
    }

    private function paginer(int $total, int $page, int $parPage): array
    {
        $pages = max(1, (int)ceil($total / $parPage));
        $page  = max(1, min($page, $pages));
        return [
            'page'    => $page,
            'parPage' => $parPage,
            'total'   => $total,
            'pages'   => $pages,
            'debut'   => ($page - 1) * $parPage + 1,
            'fin'     => min($page * $parPage, $total),
        ];
    }

    private function pointagesSemaine(
        int $chantierId,
        int $semaine,
        int $annee
    ): \Illuminate\Support\Collection {
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        return Pointage::where('chantier_id', $chantierId)
            ->whereBetween('date', [
                $samedi->toDateString(),
                $vendredi->toDateString(),
            ])
            ->get()
            ->groupBy('ouvrier_id');
    }

    private function recapsSemaine(
        int $chantierId,
        int $semaine,
        int $annee
    ): \Illuminate\Support\Collection {
        return RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get()
            ->keyBy('ouvrier_id');
    }

    private function construireLigneOuvrier(
        Personnel $ouvrier,
        array $jours,
        \Illuminate\Support\Collection $pointages,
        \Illuminate\Support\Collection $recaps
    ): array {
        $pointagesOuvrier = $pointages
            ->get($ouvrier->id, collect())
            ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

        $recap = $recaps->get($ouvrier->id);

        $joursDetails = collect($jours)->map(fn($jour) => [
            'date'   => $jour,
            'statut' => $pointagesOuvrier->get($jour->toDateString())?->statutPointage ?? null,
            'h_sup'  => (int)($pointagesOuvrier->get($jour->toDateString())?->heures_sup ?? 0),
        ]);

        return [
            'ouvrier'       => $ouvrier,
            'jours'         => $joursDetails,
            'jours_present' => $joursDetails->where('statut', 'present')->count(),
            'total_h_sup'   => $joursDetails->sum('h_sup'),
            'salaire_base'  => $recap?->salaire_base ?? 0,
            'salaire_h_sup' => $recap?->salaire_heures_sup ?? 0,
            'salaire_total' => $recap?->salaire_total ?? 0,
        ];
    }

    private function calculerSalaireOuvrier(
        Personnel $ouvrier,
        int $chantierId,
        int $semaine,
        int $annee
    ): array {
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        $pointages = Pointage::where('ouvrier_id', $ouvrier->idPersonnel)
            ->where('chantier_id', $chantierId)
            ->whereBetween('date', [
                $samedi->toDateString(),
                $vendredi->toDateString(),
            ])
            ->get();

        $joursPresents  = $pointages->where('statutPointage', 'present')->count();
        $totalHeuresSup = (int) $pointages->sum('heures_sup');

        $taux = TauxSalaire::where('poste_id', $ouvrier->poste_id)
            ->where('chantier_id', $chantierId)
            ->first();

        $salaireBase      = $taux ? $joursPresents * $taux->taux_journalier : 0;
        $salaireHeuresSup = $taux ? $totalHeuresSup * $taux->taux_heure_sup : 0;

        return [
            'jours_presents'     => $joursPresents,
            'total_heures_sup'   => $totalHeuresSup,
            'salaire_base'       => $salaireBase,
            'salaire_heures_sup' => $salaireHeuresSup,
            'salaire_total'      => $salaireBase + $salaireHeuresSup,
        ];
    }

    private function recalculerRecap(
        int $chantierId,
        int $semaine,
        int $annee
    ): void {
        $personnel = $this->personnelActif($chantierId);

        foreach ($personnel as $ouvrier) {
            $recap = RecapHebdomadaire::where('ouvrier_id', $ouvrier->idPersonnel)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->first();

            // Ne pas recalculer si déjà validé/transmis
            if ($recap && !in_array($recap->statut, ['en_attente', 'rejetee'])) {
                continue;
            }

            $donnees = $this->calculerSalaireOuvrier(
                $ouvrier,
                $chantierId,
                $semaine,
                $annee
            );

            RecapHebdomadaire::updateOrCreate(
                [
                    'ouvrier_id'  => $ouvrier->id,
                    'chantier_id' => $chantierId,
                    'semaine'     => $semaine,
                    'annee'       => $annee,
                ],
                array_merge($donnees, [
                    'statut' => $recap?->statut ?? 'en_attente',
                ])
            );
        }
    }

    // ══════════════════════════════════════════════════════════
    // FICHE JOURNALIÈRE
    // ══════════════════════════════════════════════════════════

    public function getPointagesDuJour(int $chantierId): array
    {
        $today = Carbon::today();

        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return [
            'date'        => $today,
            'pointages'   => $pointages,
            'ficheExiste' => $pointages->isNotEmpty(),
        ];
    }

    public function getToutPersonnel(int $chantierId): \Illuminate\Support\Collection
    {
        return $this->personnelActif($chantierId);
    }

    public function getPersonnelPagine(
        int $chantierId,
        int $page,
        int $parPage = 20
    ): array {
        $tous  = $this->personnelActif($chantierId);
        $total = $tous->count();
        $pg    = $this->paginer($total, $page, $parPage);

        $personnel = $tous
            ->forPage($pg['page'], $parPage)
            ->groupBy(fn($p) => $p->poste->libelle);

        return ['personnel' => $personnel, 'pagination' => $pg];
    }

    /**
     * Vérifie si la fiche du JOUR DONNÉ est modifiable.
     * On cherche le cycle (semaine) auquel appartient ce jour.
     */
    public function jourModifiable(int $chantierId, Carbon $date): bool
    {
        $semaine = SemaineHelper::numeroCycle($date);
        $annee   = SemaineHelper::anneeCycle($date);

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if ($recaps->isEmpty()) return true;

        return $recaps->every(
            fn($r) => in_array($r->statut, ['en_attente', 'rejetee'])
        );
    }

    /**
     * Alias pour la semaine courante (utilisé dans ficheJour).
     */
    public function semaineModifiable(int $chantierId): bool
    {
        return $this->jourModifiable($chantierId, Carbon::today());
    }

    public function enregistrerFiche(array $lignes, int $chantierId): void
    {
        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        if (!$this->jourModifiable($chantierId, $today)) {
            throw new \Exception(
                'La fiche de cette semaine a déjà été soumise.'
            );
        }

        foreach ($lignes as $ligne) {
            Pointage::updateOrCreate(
                [
                    'ouvrier_id'  => $ligne['ouvrier_id'],
                    'chantier_id' => $chantierId,
                    'date'        => $today->toDateString(),
                ],
                [
                    'statutPointage' => $ligne['statutPointage'],
                    'heures_sup'     => $ligne['statutPointage'] === 'present'
                        ? (int)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        $this->recalculerRecap($chantierId, $semaine, $annee);
    }

    // ══════════════════════════════════════════════════════════
    // RÉCAP HEBDOMADAIRE
    // ══════════════════════════════════════════════════════════

    public function getInfosSemaine(int $semaine, int $annee): array
    {
        return [
            'semaine' => $semaine,
            'annee'   => $annee,
            'debut'   => SemaineHelper::debutDepuisNumero($semaine, $annee),
            'fin'     => SemaineHelper::finDepuisNumero($semaine, $annee),
            'jours'   => SemaineHelper::jours($semaine, $annee),
        ];
    }

    public function getStatutSemaine(
        int $chantierId,
        int $semaine,
        int $annee
    ): array {
        $recaps = $this->recapsSemaine($chantierId, $semaine, $annee);

        return [
            'statut'      => $recaps->first()?->statut ?? 'en_attente',
            'motif_rejet' => $recaps->first()?->motif_rejet,
        ];
    }

    public function getLignesRecap(
        int $chantierId,
        int $semaine,
        int $annee,
        int $page,
        int $parPage = 15
    ): array {
        $tousPersonnel = $this->personnelActif($chantierId);
        $total         = $tousPersonnel->count();
        $pg            = $this->paginer($total, $page, $parPage);
        $jours         = SemaineHelper::jours($semaine, $annee);
        $pointages     = $this->pointagesSemaine($chantierId, $semaine, $annee);
        $recaps        = $this->recapsSemaine($chantierId, $semaine, $annee);

        $lignes = $tousPersonnel
            ->forPage($pg['page'], $parPage)
            ->values()
            ->map(fn($o) => $this->construireLigneOuvrier(
                $o,
                $jours,
                $pointages,
                $recaps
            ));

        return ['lignes' => $lignes, 'pagination' => $pg];
    }

    public function getTotauxSemaine(
        int $chantierId,
        int $semaine,
        int $annee
    ): array {
        $tousPersonnel = $this->personnelActif($chantierId);
        $jours         = SemaineHelper::jours($semaine, $annee);
        $pointages     = $this->pointagesSemaine($chantierId, $semaine, $annee);
        $recaps        = $this->recapsSemaine($chantierId, $semaine, $annee);

        $toutesLignes = $tousPersonnel->map(
            fn($o) => $this->construireLigneOuvrier($o, $jours, $pointages, $recaps)
        );

        $totauxParJour = collect($jours)->map(
            fn($jour, $i) =>
            $toutesLignes->sum(
                fn($l) => $l['jours'][$i]['statut'] === 'present' ? 1 : 0
            )
        );

        return [
            'total_presents'  => $toutesLignes->sum('jours_present'),
            'total_h_sup'     => $toutesLignes->sum('total_h_sup'),
            'total_salaires'  => $recaps->sum('salaire_total'),
            'totaux_par_jour' => $totauxParJour,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // MODIFICATION PAR JOUR (récap rejeté)
    // ══════════════════════════════════════════════════════════

    public function getPointagesDuJourPagines(
        int $chantierId,
        string $date,
        int $page,
        int $parPage = 20
    ): array {
        $tousPersonnel = $this->personnelActif($chantierId);
        $total         = $tousPersonnel->count();
        $pg            = $this->paginer($total, $page, $parPage);

        $pointagesJour = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('ouvrier_id');

        $lignes = $tousPersonnel
            ->forPage($pg['page'], $parPage)
            ->values()
            ->map(fn($ouvrier) => [
                'ouvrier' => $ouvrier,
                'statut'  => $pointagesJour->get($ouvrier->id)
                    ?->statutPointage ?? 'absent',
                'h_sup'   => (int)($pointagesJour->get($ouvrier->id)
                    ?->heures_sup ?? 0),
            ]);

        return ['lignes' => $lignes, 'pagination' => $pg];
    }

    public function modifierPointageJour(
        int $chantierId,
        string $date,
        array $lignes
    ): void {
        $dateCarbon = Carbon::parse($date);
        $semaine    = SemaineHelper::numeroCycle($dateCarbon);
        $annee      = SemaineHelper::anneeCycle($dateCarbon);

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if ($recaps->isNotEmpty() && !$recaps->every(
            fn($r) => in_array($r->statut, ['en_attente', 'rejetee'])
        )) {
            throw new \Exception('Ce récap ne peut plus être modifié.');
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
                        ? (int)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        $this->recalculerRecap($chantierId, $semaine, $annee);
    }

    // ══════════════════════════════════════════════════════════
    // SOUMISSION / VALIDATION / REJET
    // ══════════════════════════════════════════════════════════

    public function soumettreSemaine(int $chantierId, int $pointeurId): void
    {
        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        $this->recalculerRecap($chantierId, $semaine, $annee);

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
    // CALCUL DES SALAIRES
    // ══════════════════════════════════════════════════════════

    public function calculerSalaires(
        int $chantierId,
        int $semaine,
        int $annee
    ): void {
        $personnel = $this->personnelActif($chantierId);

        foreach ($personnel as $ouvrier) {
            $donnees = $this->calculerSalaireOuvrier(
                $ouvrier,
                $chantierId,
                $semaine,
                $annee
            );

            RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->update(array_merge($donnees, [
                    'statut' => 'envoyee_direction',
                ]));
        }
    }

    // ══════════════════════════════════════════════════════════
    // TEMPS RÉEL (dashboard pointeur)
    // ══════════════════════════════════════════════════════════

    public function getPointagesJourTempsReel(
        int $chantierId
    ): \Illuminate\Support\Collection {
        $today     = Carbon::today();
        $personnel = $this->personnelActif($chantierId);

        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return $personnel->map(fn($p) => [
            'ouvrier'    => $p,
            'statut'     => $pointages->get($p->id)
                ?->statutPointage ?? 'non_pointe',
            'heures_sup' => (int)($pointages->get($p->id)
                ?->heures_sup ?? 0),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // SEMAINES DISPONIBLES (pour les sélecteurs)
    // ══════════════════════════════════════════════════════════

    public function getSemainesDisponibles(int $nbSemaines = 12): \Illuminate\Support\Collection
    {
        return collect(range(0, $nbSemaines - 1))->map(function ($i) {
            // Reculer de i cycles de 7 jours depuis aujourd'hui
            $date    = Carbon::today()->subDays($i * 7);
            $semaine = SemaineHelper::numeroCycle($date);
            $annee   = SemaineHelper::anneeCycle($date);

            return [
                'semaine' => $semaine,
                'annee'   => $annee,
                'label'   => SemaineHelper::libelle($semaine, $annee),
            ];
        })->unique(fn($s) => $s['semaine'] . '-' . $s['annee']);
    }
}

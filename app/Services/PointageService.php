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
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════

    // Retourne les 7 jours de la semaine (sam → ven)
    private function joursDeLaSemaine(int $annee, int $semaine): array
    {
        // Samedi de la semaine précédente = début de notre cycle
        $samedi = Carbon::now()
            ->setISODate($annee, $semaine)
            ->startOfWeek() // lundi
            ->subDays(2);   // → samedi précédent

        $jours = [];
        for ($i = 0; $i <= 6; $i++) {
            $jours[] = $samedi->copy()->addDays($i);
        }
        return $jours; // Sam, Dim, Lun, Mar, Mer, Jeu, Ven
    }

    // Retourne les pointages d'un chantier sur une semaine, indexés par ouvrier
    private function pointagesSemaine(int $chantierId, int $semaine, int $annee): \Illuminate\Support\Collection
    {
        $samedi   = Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->subDays(2);
        $vendredi = $samedi->copy()->addDays(6);

        return Pointage::where('chantier_id', $chantierId)
            ->whereBetween('date', [
                $samedi->toDateString(),
                $vendredi->toDateString()
            ])
            ->get()
            ->groupBy('ouvrier_id');
    }

    // Retourne les récaps d'un chantier pour une semaine, indexés par ouvrier
    private function recapsSemaine(int $chantierId, int $semaine, int $annee): \Illuminate\Support\Collection
    {
        return RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get()
            ->keyBy('ouvrier_id');
    }

    // Retourne tous les ouvriers actifs d'un chantier
    private function personnelActif(int $chantierId): \Illuminate\Support\Collection
    {
        return Personnel::query()
            ->select('personnels.*')
            ->join('postes', 'personnels.poste_id', '=', 'postes.id') // Ajustez 'postes.id' si votre clé a un autre nom
            ->where('personnels.chantier_id', $chantierId)
            ->where('personnels.statutPersonnel', 'actif')
            // 1. Regroupe par corps de métier (en retirant le mot 'chef')
            ->orderByRaw("TRIM(REPLACE(LOWER(postes.libelle), 'chef', '')) ASC")
            // 2. Met le chef en premier dans son groupe
            ->orderByRaw("
            CASE 
                WHEN LOWER(postes.libelle) LIKE 'chef%' THEN 0 
                ELSE 1 
            END ASC
        ")
            // 3. Trie les ouvriers du même rang par ordre alphabétique
            ->orderBy('personnels.nomPersonnel', 'asc')
            ->with('poste')
            ->get();
    }

    // Construit l'objet pagination
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

    // ══════════════════════════════════════════════════════════
    // FICHE JOURNALIÈRE
    // ══════════════════════════════════════════════════════════

    // Retourne TOUS les ouvriers actifs (pour les champs cachés du formulaire)
    public function getToutPersonnel(int $chantierId): \Illuminate\Support\Collection
    {
        return $this->personnelActif($chantierId);
    }

    // Retourne les ouvriers actifs paginés pour la fiche du jour
    public function getPersonnelPagine(int $chantierId, int $page, int $parPage = 20): array
    {
        $tous  = $this->personnelActif($chantierId);
        $total = $tous->count();
        $pg    = $this->paginer($total, $page, $parPage);

        $personnel = $tous
            ->forPage($pg['page'], $parPage)
            ->groupBy(fn($p) => $p->poste->libelle);

        return [
            'personnel'  => $personnel,
            'pagination' => $pg,
        ];
    }

    // Retourne les pointages du jour pour un chantier
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

    // Enregistre les pointages du jour
    public function enregistrerFiche(array $lignes, int $chantierId): void
    {
        if (!$this->semaineModifiable($chantierId)) {
            throw new \Exception(
                'La fiche hebdomadaire a déjà été soumise. Modification impossible.'
            );
        }

        $today = Carbon::today()->toDateString();

        foreach ($lignes as $ligne) {
            Pointage::updateOrCreate(
                [
                    'ouvrier_id'  => $ligne['ouvrier_id'],
                    'chantier_id' => $chantierId,
                    'date'        => $today,
                ],
                [
                    'statutPointage' => $ligne['statutPointage'],
                    'heures_sup'     => $ligne['statutPointage'] === 'present'
                        ? (float)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        $this->recalculerRecap($chantierId, Carbon::today()->isoWeek(), Carbon::today()->year);
    }

    // ══════════════════════════════════════════════════════════
    // SEMAINE — STATUT
    // ══════════════════════════════════════════════════════════

    // Vérifie si la semaine en cours est modifiable
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

    // Retourne le statut global de la semaine
    public function getStatutSemaine(int $chantierId, int $semaine, int $annee): array
    {
        $recaps = $this->recapsSemaine($chantierId, $semaine, $annee);

        return [
            'statut'      => $recaps->first()?->statut ?? 'en_attente',
            'motif_rejet' => $recaps->first()?->motif_rejet,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // RÉCAP HEBDOMADAIRE — LECTURE
    // ══════════════════════════════════════════════════════════

    // Infos générales de la semaine (dates, jours)
    public function getInfosSemaine(int $semaine, int $annee): array
    {
        $samedi   = Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->subDays(2);
        $vendredi = $samedi->copy()->addDays(6);

        return [
            'semaine' => $semaine,
            'annee'   => $annee,
            'debut'   => $samedi,
            'fin'     => $vendredi,
            'jours'   => $this->joursDeLaSemaine($annee, $semaine),
        ];
    }

    // Construit les lignes du récap paginées
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
        $jours         = $this->joursDeLaSemaine($annee, $semaine);
        $pointages     = $this->pointagesSemaine($chantierId, $semaine, $annee);
        $recaps        = $this->recapsSemaine($chantierId, $semaine, $annee);

        $lignes = $tousPersonnel
            ->forPage($pg['page'], $parPage)
            ->values()
            ->map(function ($ouvrier) use ($jours, $pointages, $recaps) {
                return $this->construireLigneOuvrier($ouvrier, $jours, $pointages, $recaps);
            });

        return [
            'lignes'     => $lignes,
            'pagination' => $pg,
        ];
    }

    // Calcule les totaux globaux (toutes pages)
    public function getTotauxSemaine(int $chantierId, int $semaine, int $annee): array
    {
        $tousPersonnel = $this->personnelActif($chantierId);
        $jours         = $this->joursDeLaSemaine($annee, $semaine);
        $pointages     = $this->pointagesSemaine($chantierId, $semaine, $annee);
        $recaps        = $this->recapsSemaine($chantierId, $semaine, $annee);

        $toutesLignes = $tousPersonnel->map(
            fn($o) => $this->construireLigneOuvrier($o, $jours, $pointages, $recaps)
        );

        $totauxParJour = collect($jours)->map(
            fn($jour, $i) =>
            $toutesLignes->sum(fn($l) => $l['jours'][$i]['statut'] === 'present' ? 1 : 0)
        );

        return [
            'total_presents'  => $toutesLignes->sum('jours_present'),
            'total_h_sup'     => $toutesLignes->sum('total_h_sup'),
            'total_salaires'  => $recaps->sum('salaire_total'),
            'totaux_par_jour' => $totauxParJour,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // MODIFICATION PAR JOUR (recap rejeté)
    // ══════════════════════════════════════════════════════════

    // Retourne les pointages d'un jour donné paginés (pour la modification)
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
                'statut'  => $pointagesJour->get($ouvrier->id)?->statutPointage ?? 'absent',
                'h_sup'   => (float)($pointagesJour->get($ouvrier->id)?->heures_sup ?? 0),
            ]);

        return [
            'lignes'     => $lignes,
            'pagination' => $pg,
        ];
    }

    // Modifie les pointages d'un jour (depuis le récap rejeté)
    public function modifierPointageJour(
        int $chantierId,
        string $date,
        array $lignes
    ): void {
        $semaine = Carbon::parse($date)->isoWeek();
        $annee   = Carbon::parse($date)->year;

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
                        ? (float)($ligne['heures_sup'] ?? 0) : 0,
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
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

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

    public function calculerSalaires(int $chantierId, int $semaine, int $annee): void
    {
        $personnel = $this->personnelActif($chantierId);

        foreach ($personnel as $ouvrier) {
            $donnees = $this->calculerSalaireOuvrier($ouvrier, $chantierId, $semaine, $annee);

            RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->update(array_merge($donnees, ['statut' => 'envoyee_direction']));
        }
    }

    // ══════════════════════════════════════════════════════════
    // TEMPS RÉEL
    // ══════════════════════════════════════════════════════════

    public function getPointagesJourTempsReel(int $chantierId): \Illuminate\Support\Collection
    {
        $today     = Carbon::today();
        $personnel = $this->personnelActif($chantierId);

        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return $personnel->map(fn($p) => [
            'ouvrier'    => $p,
            'statut'     => $pointages->get($p->id)?->statutPointage ?? 'non_pointe',
            'heures_sup' => $pointages->get($p->id)?->heures_sup ?? 0,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // CALCULS INTERNES PRIVÉS
    // ══════════════════════════════════════════════════════════

    // Construit la ligne d'un ouvrier pour le récap
    private function construireLigneOuvrier(
        Personnel $ouvrier,
        array $jours,
        \Illuminate\Support\Collection $pointages,
        \Illuminate\Support\Collection $recaps
    ): array {
        $pointagesOuvrier = $pointages->get($ouvrier->id, collect())
            ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

        $recap = $recaps->get($ouvrier->id);

        $joursDetails = collect($jours)->map(fn($jour) => [
            'date'   => $jour,
            'statut' => $pointagesOuvrier->get($jour->toDateString())?->statutPointage ?? null,
            'h_sup'  => $pointagesOuvrier->get($jour->toDateString())?->heures_sup ?? 0,
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

    // Recalcule le récap hebdomadaire d'un chantier
    private function recalculerRecap(int $chantierId, int $semaine, int $annee): void
    {
        $personnel = $this->personnelActif($chantierId);

        foreach ($personnel as $ouvrier) {
            $recap = RecapHebdomadaire::where('ouvrier_id', $ouvrier->id)
                ->where('chantier_id', $chantierId)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->first();

            if ($recap && !in_array($recap->statut, ['en_attente', 'rejetee'])) {
                continue;
            }

            $donnees = $this->calculerSalaireOuvrier($ouvrier, $chantierId, $semaine, $annee);

            RecapHebdomadaire::updateOrCreate(
                [
                    'ouvrier_id'  => $ouvrier->id,
                    'chantier_id' => $chantierId,
                    'semaine'     => $semaine,
                    'annee'       => $annee,
                ],
                array_merge($donnees, ['statut' => $recap?->statut ?? 'en_attente'])
            );
        }
    }

    private function calculerSalaireOuvrier(Personnel $ouvrier, int $chantierId, int $semaine, int $annee): array
    {
        $samedi   = Carbon::now()->setISODate($annee, $semaine)->startOfWeek()->subDays(2);
        $vendredi = $samedi->copy()->addDays(6);

        $pointages = Pointage::where('ouvrier_id', $ouvrier->id)
            ->where('chantier_id', $chantierId)
            ->whereBetween('date', [$samedi->toDateString(), $vendredi->toDateString()])
            ->get();

        $joursPresents  = $pointages->where('statutPointage', 'present')->count();
        $totalHeuresSup = $pointages->sum('heures_sup');

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
}

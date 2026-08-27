<?php

namespace App\Services;

use App\Helpers\PointageHelper;
use App\Helpers\SemaineHelper;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use Carbon\Carbon;

class PointageService
{
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
        return PointageHelper::personnelActif($chantierId);
    }

    public function getPersonnelPagine(int $chantierId, int $page, int $parPage = 20): array
    {
        $tous  = PointageHelper::personnelActif($chantierId);
        $total = $tous->count();
        $pg    = PointageHelper::paginer($total, $page, $parPage);

        $personnel = $tous->forPage($pg['page'], $parPage)
            ->groupBy(fn($p) => $p->poste->libelle);

        return ['personnel' => $personnel, 'pagination' => $pg];
    }

    public function jourModifiable(int $chantierId, Carbon $date): bool
    {
        $semaine = SemaineHelper::numeroCycle($date);
        $annee   = SemaineHelper::anneeCycle($date);

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if ($recaps->isEmpty()) return true;

        return $recaps->every(fn($r) => in_array($r->statutRecap, ['en_attente', 'rejetee']));
    }

    public function semaineModifiable(int $chantierId): bool
    {
        return $this->jourModifiable($chantierId, Carbon::today());
    }

    public function enregistrerFiche(array $lignes, int $chantierId, RecapService $recapService): void
    {
        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        if (!$this->jourModifiable($chantierId, $today)) {
            throw new \Exception('La fiche de cette semaine a déjà été soumise.');
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
                    'heures_sup'     => $ligne['statutPointage'] === 'present' ? (int)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        $recapService->recalculerRecap($chantierId, $semaine, $annee);
    }

    public function getPointagesDuJourPagines(int $chantierId, string $date, int $page, int $parPage = 20): array
    {
        $tousPersonnel = PointageHelper::personnelActif($chantierId);
        $total         = $tousPersonnel->count();
        $pg            = PointageHelper::paginer($total, $page, $parPage);

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
                'h_sup'   => (int)($pointagesJour->get($ouvrier->id)?->heures_sup ?? 0),
            ]);

        return ['lignes' => $lignes, 'pagination' => $pg];
    }

    public function modifierPointageJour(int $chantierId, string $date, array $lignes, RecapService $recapService): void
    {
        $dateCarbon = Carbon::parse($date);
        $semaine    = SemaineHelper::numeroCycle($dateCarbon);
        $annee      = SemaineHelper::anneeCycle($dateCarbon);

        $recaps = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get();

        if ($recaps->isNotEmpty() && !$recaps->every(fn($r) => in_array($r->statutRecap, ['en_attente', 'rejetee']))) {
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
                    'heures_sup'     => $ligne['statutPointage'] === 'present' ? (int)($ligne['heures_sup'] ?? 0) : 0,
                ]
            );
        }

        $recapService->recalculerRecap($chantierId, $semaine, $annee);
    }

    public function getPointagesJourTempsReel(int $chantierId): \Illuminate\Support\Collection
    {
        $today     = Carbon::today();
        $personnel = PointageHelper::personnelActif($chantierId);

        $pointages = Pointage::where('chantier_id', $chantierId)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('ouvrier_id');

        return $personnel->map(fn($p) => [
            'ouvrier'    => $p,
            'statut'     => $pointages->get($p->id)?->statutPointage ?? 'non_pointe',
            'heures_sup' => (int)($pointages->get($p->id)?->heures_sup ?? 0),
        ]);
    }
}

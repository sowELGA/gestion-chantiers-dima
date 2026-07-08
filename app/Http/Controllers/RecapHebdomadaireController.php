<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use App\Services\PdfService;
use Carbon\Carbon;

class RecapHebdomadaireController extends Controller
{
    public function __construct(
        private PdfService $pdfService
    ) {}

    // Liste des fiches validées par le chef de projet
    public function index()
    {
        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);

        // Chantiers ayant des recaps validés pour la semaine sélectionnée
        $chantiers = Chantier::with(['recapsHebdomadaires' => function ($q) use ($semaine, $annee) {
            $q->where('semaine', $semaine)
                ->where('annee', $annee)
                ->whereIn('statut', ['validee_cp', 'envoyee_direction']);
        }])
            ->whereHas('recapsHebdomadaires', function ($q) use ($semaine, $annee) {
                $q->where('semaine', $semaine)
                    ->where('annee', $annee)
                    ->whereIn('statut', ['validee_cp', 'envoyee_direction']);
            })
            ->get();

        // Générer la liste des semaines disponibles (52 semaines en arrière)
        $semaines = collect();
        for ($i = 0; $i <= 51; $i++) {
            $date = Carbon::today()->subWeeks($i);
            $semaines->push([
                'semaine' => $date->isoWeek(),
                'annee'   => $date->year,
                'label'   => 'Semaine ' . $date->isoWeek() . ' — ' .
                    $date->startOfWeek()->format('d/m') . ' au ' .
                    $date->endOfWeek()->format('d/m/Y'),
            ]);
        }

        return view(
            'direction.salaires.recaps',
            compact('chantiers', 'semaine', 'annee', 'semaines')
        );
    }

    // Générer le PDF
    public function genererPdf(Chantier $chantier)
    {
        $semaine = request('semaine');
        $annee   = request('annee');

        return $this->pdfService->genererFichePaie(
            $chantier->id,
            $semaine,
            $annee
        );
    }

    // Aperçu détaillé d'une fiche avant génération PDF
    public function apercu(Chantier $chantier)
    {
        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);

        $recaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            ->get()
            ->groupBy(fn($r) => $r->ouvrier->poste->libelle);

        $debutSemaine = Carbon::now()
            ->setISODate($annee, $semaine)
            ->startOfWeek()
            ->locale('fr')
            ->isoFormat('D MMMM YYYY');

        $finSemaine = Carbon::now()
            ->setISODate($annee, $semaine)
            ->endOfWeek()
            ->locale('fr')
            ->isoFormat('D MMMM YYYY');

        $totalGeneral = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            ->sum('salaire_total');

        $statut = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->value('statut');

        return view('direction.salaires.apercu', compact(
            'chantier',
            'recaps',
            'semaine',
            'annee',
            'debutSemaine',
            'finSemaine',
            'totalGeneral',
            'statut'
        ));
    }
}

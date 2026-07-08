<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfService
{
    public function genererFichePaie(int $chantierId, int $semaine, int $annee)
    {
        $chantier = Chantier::findOrFail($chantierId);

        $recaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantierId)
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

        $totalGeneral = RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            ->sum('salaire_total');

        $pdf = Pdf::loadView('pdf.fiche-paie', compact(
            'chantier',
            'recaps',
            'semaine',
            'annee',
            'debutSemaine',
            'finSemaine',
            'totalGeneral'
        ))->setPaper('a4', 'landscape');

        // Marquer comme envoyée à la direction
        RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'validee_cp')
            ->update(['statut' => 'envoyee_direction']);

        return $pdf->download(
            "fiche-paie-{$chantier->nomChantier}-S{$semaine}-{$annee}.pdf"
        );
    }
}

<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    // ══════════════════════════════════════════════════════════
    // HELPER — Calcule Samedi → Vendredi d'une semaine ISO
    // ══════════════════════════════════════════════════════════

    private function getSamedi(int $annee, int $semaine): Carbon
    {
        // Lundi de la semaine ISO → recule 2 jours → Samedi précédent
        return Carbon::now()
            ->setISODate($annee, $semaine)
            ->startOfWeek()   // Lundi
            ->subDays(2);     // Samedi
    }

    private function getVendredi(int $annee, int $semaine): Carbon
    {
        return $this->getSamedi($annee, $semaine)->copy()->addDays(6);
    }

    // ══════════════════════════════════════════════════════════
    // GÉNÉRATION FICHE DE PAIE PDF
    // ══════════════════════════════════════════════════════════

    public function genererFichePaie(int $chantierId, int $semaine, int $annee)
    {
        $chantier = Chantier::findOrFail($chantierId);
        $samedi   = $this->getSamedi($annee, $semaine);
        $vendredi = $this->getVendredi($annee, $semaine);

        // Récaps groupés par poste — uniquement les ouvriers avec salaire
        $recaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'envoyee_direction')
            ->where('salaire_total', '>', 0)
            ->get()
            ->groupBy(fn($r) => $r->ouvrier->poste->libelle)
            ->sortKeys();

        $totalGeneral = $recaps->flatten()->sum('salaire_total');

        $debutSemaine = $samedi->locale('fr')->isoFormat('D MMMM YYYY');
        $finSemaine   = $vendredi->locale('fr')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('pdf.fiche-paie', compact(
            'chantier',
            'recaps',
            'semaine',
            'annee',
            'samedi',
            'vendredi',
            'debutSemaine',
            'finSemaine',
            'totalGeneral'
        ))->setPaper('A4', 'landscape');

        $nomFichier = 'fiche-paie-'
            . str($chantier->nomChantier)->slug()
            . '-S' . $semaine
            . '-' . $annee
            . '.pdf';

        return $pdf->download($nomFichier);
    }
}

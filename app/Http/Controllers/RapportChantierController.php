<?php

namespace App\Http\Controllers;

use App\Http\Requests\RapportRequest;
use App\Models\Chantier;
use App\Models\RapportChantier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class RapportChantierController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET — Ses rapports
    // ══════════════════════════════════════════════════════════

    public function index()
    {
        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->orderBy('nomChantier')
            ->get();

        $chantierId = request('chantier_id', $chantiers->first()?->id);
        $type       = request('type', 'tous');

        $query = RapportChantier::with(['auteur', 'chantier'])
            ->where('auteur_id', auth()->id())
            ->orderByDesc('date_rapport');

        if ($chantierId) {
            $query->where('chantier_id', $chantierId);
        }

        if ($type !== 'tous') {
            $query->where('type', $type);
        }

        $rapports = $query->paginate(10)->withQueryString();

        return view(
            'chef_projet.rapports.index',
            compact('rapports', 'chantiers', 'chantierId', 'type')
        );
    }

    public function create()
    {
        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->whereIn('statut', ['en_cours', 'suspendu'])
            ->orderBy('nomChantier')
            ->get();

        return view('chef_projet.rapports.create', compact('chantiers'));
    }

    public function store(RapportRequest $request)
    {
        RapportChantier::create(array_merge(
            $request->validated(),
            ['auteur_id' => auth()->id()]
        ));

        return redirect()
            ->route('chef_projet.rapports.index')
            ->with('success', 'Rapport ajouté avec succès.');
    }

    public function edit(RapportChantier $rapport)
    {
        abort_if($rapport->auteur_id !== auth()->id(), 403);

        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->whereIn('statut', ['en_cours', 'suspendu'])
            ->orderBy('nomChantier')
            ->get();

        return view(
            'chef_projet.rapports.edit',
            compact('rapport', 'chantiers')
        );
    }

    public function update(RapportRequest $request, RapportChantier $rapport)
    {
        abort_if($rapport->auteur_id !== auth()->id(), 403);

        $rapport->update($request->validated());

        return redirect()
            ->route('chef_projet.rapports.index')
            ->with('success', 'Rapport modifié avec succès.');
    }

    public function destroy(RapportChantier $rapport)
    {
        abort_if($rapport->auteur_id !== auth()->id(), 403);

        $rapport->delete();

        return back()->with('success', 'Rapport supprimé.');
    }

    public function show(RapportChantier $rapport)
    {
        abort_if($rapport->auteur_id !== auth()->id(), 403);

        return view('chef_projet.rapports.show', compact('rapport'));
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — Tous les rapports avec filtres
    // ══════════════════════════════════════════════════════════

    public function indexDirection()
    {
        $chantiers = Chantier::orderBy('nomChantier')->get();

        $chantierId = request('chantier_id');
        $type       = request('type', 'tous');
        $recherche  = request('recherche');

        // Validées explicitement : whereDate() sur une valeur malformée
        // provoquerait sinon une erreur SQL opaque au lieu d'un message clair.
        $filtresDate = request()->validate([
            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',
        ]);
        $dateDebut = $filtresDate['date_debut'] ?? null;
        $dateFin   = $filtresDate['date_fin'] ?? null;

        $query = RapportChantier::with(['auteur', 'chantier'])
            ->orderByDesc('date_rapport');

        if ($chantierId) {
            $query->where('chantier_id', $chantierId);
        }

        if ($type !== 'tous') {
            $query->where('type', $type);
        }

        if ($dateDebut) {
            $query->whereDate('date_rapport', '>=', $dateDebut);
        }

        if ($dateFin) {
            $query->whereDate('date_rapport', '<=', $dateFin);
        }

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                    ->orWhere('contenu', 'like', '%' . $recherche . '%');
            });
        }

        $rapports = $query->paginate(12)->withQueryString();

        // Statistiques globales
        $stats = [
            'total'     => RapportChantier::count(),
            'ce_mois'   => RapportChantier::whereMonth(
                'date_rapport',
                now()->month
            )->whereYear('date_rapport', now()->year)->count(),
            'incidents' => RapportChantier::where('type', 'incident')->count(),
            // "distinct('chantier_id')->count()" (sans argument à count())
            // ne génère pas de façon fiable COUNT(DISTINCT chantier_id) selon
            // les versions de Laravel/le driver SQL — la forme correcte est
            // distinct() (sans colonne) suivi de count('chantier_id').
            'chantiers' => RapportChantier::distinct()->count('chantier_id'),
        ];

        return view('direction.rapports.index', compact(
            'rapports',
            'chantiers',
            'stats',
            'chantierId',
            'type',
            'dateDebut',
            'dateFin',
            'recherche'
        ));
    }

    public function showDirection(RapportChantier $rapport)
    {
        $rapport->load(['auteur', 'chantier']);
        return view('direction.rapports.show', compact('rapport'));
    }

    public function telechargerPdf(RapportChantier $rapport)
    {
        $rapport->load(['auteur', 'chantier']);

        $pdf = Pdf::loadView('pdf.rapport-chantier', compact('rapport'))
            ->setPaper('A4', 'portrait');

        $nomFichier = 'rapport-'
            . Str::slug($rapport->titre ?: 'sans-titre')
            . '-' . $rapport->id
            . '.pdf';

        return $pdf->download($nomFichier);
    }
}

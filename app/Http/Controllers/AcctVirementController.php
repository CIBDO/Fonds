<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Models\Poste;
use App\Models\Virement;
use App\Notifications\VirementPaye;
use App\Services\ExcelVirementService;
use App\Services\MailAlerteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcctVirementController extends Controller
{
    public function index(Request $request)
    {
        $query = Virement::with(['poste', 'banque', 'bordereau'])
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));

        $this->applyFilters($query, $request);

        $virements = $query->orderByDesc('annee')->orderBy('mois')->orderBy('poste_id')->paginate(30)->withQueryString();
        $postes = Poste::orderBy('nom')->get();
        $banques = Banque::actives()->orderBy('nom')->get();

        return view('virements.acct.index', compact('virements', 'postes', 'banques'));
    }

    public function show(Virement $virement)
    {
        abort_unless($virement->bordereau && $virement->bordereau->isSoumis(), 404);
        $virement->load(['poste', 'banque', 'bordereau.demandeFonds', 'lignes', 'payePar']);

        return view('virements.acct.show', compact('virement'));
    }

    public function consolidation(Request $request)
    {
        $query = Virement::query()
            ->with('banque')
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));

        $this->applyFilters($query, $request);

        $virements = $query->get();
        $parBanque = $virements->groupBy('banque_id')->map(function ($group) {
            $banque = $group->first()->banque;

            return [
                'banque_id' => $banque->id ?? null,
                'banque' => $banque->nom ?? 'Inconnue',
                'nb' => $group->count(),
                'total' => (float) $group->sum('montant'),
                'paye' => (float) $group->where('statut_paiement', 'paye')->sum('montant'),
                'non_paye' => (float) $group->where('statut_paiement', 'non_paye')->sum('montant'),
            ];
        })->sortBy('banque')->values();

        $postes = Poste::orderBy('nom')->get();
        $banques = Banque::actives()->orderBy('nom')->get();

        return view('virements.acct.consolidation', compact('parBanque', 'postes', 'banques', 'virements'));
    }

    public function marquerPaye(Request $request)
    {
        $data = $request->validate([
            'virement_ids' => 'required|array|min:1',
            'virement_ids.*' => 'integer|exists:virements,id',
            'observation' => 'nullable|string|max:1000',
        ]);

        $virements = Virement::with(['poste', 'banque', 'bordereau'])
            ->whereIn('id', $data['virement_ids'])
            ->where('statut_paiement', 'non_paye')
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'))
            ->get();

        if ($virements->isEmpty()) {
            return back()->with('error', 'Aucun VIR non payé sélectionné.');
        }

        DB::transaction(function () use ($virements, $data) {
            foreach ($virements as $virement) {
                $virement->update([
                    'statut_paiement' => 'paye',
                    'date_paiement' => now(),
                    'paye_par' => Auth::id(),
                    'observation' => $data['observation'] ?? $virement->observation,
                ]);
            }
        });

        $fresh = Virement::with(['poste', 'banque'])->whereIn('id', $virements->pluck('id'))->get();
        MailAlerteService::notifyEvenement('vir_paiement', new VirementPaye($fresh));

        return back()->with('success', $virements->count().' VIR marqué(s) payé(s).');
    }

    public function exportExcel(Request $request)
    {
        $query = Virement::with(['poste', 'banque'])
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));
        $this->applyFilters($query, $request);

        return ExcelVirementService::exportVirements($query->orderBy('banque_id')->get());
    }

    public function exportPdf(Request $request)
    {
        $query = Virement::with(['poste', 'banque', 'lignes'])
            ->withCount('lignes')
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));
        $this->applyFilters($query, $request);

        $virements = $query->orderBy('banque_id')->orderBy('poste_id')->get();
        $intituleOfficiel = 'AGENCE COMPTABLE CENTRALE DU TRÉSOR';
        $filtreLibelle = $this->libelleFiltres($request);

        $pdf = Pdf::loadView('virements.pdf.liste', compact(
            'virements',
            'intituleOfficiel',
            'filtreLibelle'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Liste_VIR_'.now()->format('Ymd_His').'.pdf');
    }

    public function pdf(Virement $virement)
    {
        abort_unless($virement->bordereau && $virement->bordereau->isSoumis(), 404);
        $virement->load(['poste', 'banque', 'bordereau.demandeFonds', 'lignes', 'payePar']);

        $poste = $virement->poste;
        $intituleOfficiel = $poste?->intitulePourEntete() ?? 'AGENCE COMPTABLE CENTRALE DU TRÉSOR';
        $regionLibelle = $poste?->regionPourEntete();
        $posteNom = $poste?->libelleAffiche() ?? ($poste->nom ?? '');
        $signatureLibelle = "L'Agent Comptable Central du Trésor";

        $pdf = Pdf::loadView('virements.pdf.fiche-vir', compact(
            'virement',
            'intituleOfficiel',
            'regionLibelle',
            'posteNom',
            'signatureLibelle'
        ))->setPaper('a4', 'landscape');

        $numero = preg_replace('/[^\w\-]+/', '_', (string) $virement->numero_vir);

        return $pdf->download("Fiche_VIR_{$numero}.pdf");
    }

    public function exportConsolidationExcel(Request $request)
    {
        $query = Virement::with('banque')
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));
        $this->applyFilters($query, $request);

        $rows = $query->get()->groupBy('banque_id')->map(function ($group) {
            $banque = $group->first()->banque;

            return [
                'banque' => $banque->nom ?? 'Inconnue',
                'nb' => $group->count(),
                'total' => (float) $group->sum('montant'),
                'paye' => (float) $group->where('statut_paiement', 'paye')->sum('montant'),
                'non_paye' => (float) $group->where('statut_paiement', 'non_paye')->sum('montant'),
            ];
        })->values()->all();

        return ExcelVirementService::exportConsolidation($rows);
    }

    public function exportConsolidationPdf(Request $request)
    {
        $query = Virement::with('banque')
            ->whereHas('bordereau', fn ($q) => $q->where('status', 'soumis'));
        $this->applyFilters($query, $request);

        $parBanque = $query->get()->groupBy('banque_id')->map(function ($group) {
            $banque = $group->first()->banque;

            return [
                'banque' => $banque->nom ?? 'Inconnue',
                'nb' => $group->count(),
                'total' => (float) $group->sum('montant'),
                'paye' => (float) $group->where('statut_paiement', 'paye')->sum('montant'),
                'non_paye' => (float) $group->where('statut_paiement', 'non_paye')->sum('montant'),
            ];
        })->sortBy('banque')->values();

        $intituleOfficiel = 'AGENCE COMPTABLE CENTRALE DU TRÉSOR';
        $filtreLibelle = $this->libelleFiltres($request);

        $pdf = Pdf::loadView('virements.pdf.consolidation', compact(
            'parBanque',
            'intituleOfficiel',
            'filtreLibelle'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Consolidation_VIR_'.now()->format('Ymd_His').'.pdf');
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->integer('poste_id'));
        }
        if ($request->filled('banque_id')) {
            $query->where('banque_id', $request->integer('banque_id'));
        }
        if ($request->filled('mois')) {
            $query->where('mois', $request->string('mois'));
        }
        if ($request->filled('annee')) {
            $query->where('annee', $request->integer('annee'));
        }
        if ($request->filled('statut_paiement')) {
            $query->where('statut_paiement', $request->string('statut_paiement'));
        }
        if ($request->filled('numero_vir')) {
            $query->where('numero_vir', 'like', '%'.$request->string('numero_vir').'%');
        }
    }

    private function libelleFiltres(Request $request): string
    {
        $parts = [];

        if ($request->filled('numero_vir')) {
            $parts[] = 'N° VIR : '.$request->string('numero_vir');
        }
        if ($request->filled('poste_id')) {
            $poste = Poste::find($request->integer('poste_id'));
            if ($poste) {
                $parts[] = 'Poste : '.$poste->libelleAffiche();
            }
        }
        if ($request->filled('banque_id')) {
            $banque = Banque::find($request->integer('banque_id'));
            if ($banque) {
                $parts[] = 'Banque : '.$banque->nom;
            }
        }
        if ($request->filled('mois')) {
            $parts[] = 'Mois : '.$request->string('mois');
        }
        if ($request->filled('annee')) {
            $parts[] = 'Année : '.$request->integer('annee');
        }
        if ($request->filled('statut_paiement')) {
            $parts[] = 'Statut : '.($request->string('statut_paiement') === 'paye' ? 'Payé' : 'Non payé');
        }

        return $parts === [] ? 'Ensemble des VIR soumis' : implode(' · ', $parts);
    }
}

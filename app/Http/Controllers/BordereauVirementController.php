<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Models\BordereauVirement;
use App\Models\DemandeFonds;
use App\Models\LigneVirement;
use App\Models\User;
use App\Models\Virement;
use App\Notifications\BordereauVirementSoumis;
use App\Services\ExcelVirementService;
use App\Services\MailAlerteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BordereauVirementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = BordereauVirement::with(['demandeFonds', 'poste', 'virements.banque'])
            ->latest();

        if ($user->hasRole('tresorier') && ! $user->hasRole('admin')) {
            $query->where('poste_id', $user->poste_id);
        }

        $bordereaux = $query->paginate(20);
        $demandesApprouvees = DemandeFonds::query()
            ->where('status', 'approuve')
            ->when($user->hasRole('tresorier') && ! $user->hasRole('admin'), fn ($q) => $q->where('poste_id', $user->poste_id))
            ->whereDoesntHave('bordereauVirement')
            ->with('poste')
            ->orderByDesc('annee')
            ->orderBy('mois')
            ->get();

        return view('virements.poste.index', compact('bordereaux', 'demandesApprouvees'));
    }

    public function create(Request $request)
    {
        $demande = $this->findDemandeApprouvee($request->integer('demande_fonds_id'));
        $this->authorizePoste($demande->poste_id);

        return view('virements.poste.create', [
            'demande' => $demande,
            'banques' => Banque::actives()->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'demande_fonds_id' => 'required|exists:demande_fonds,id',
            'periode_debut' => 'nullable|date',
            'periode_fin' => 'nullable|date|after_or_equal:periode_debut',
            'fichier_joint' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls|max:10240',
        ]);

        $demande = $this->findDemandeApprouvee((int) $data['demande_fonds_id']);
        $this->authorizePoste($demande->poste_id);

        if (BordereauVirement::where('demande_fonds_id', $demande->id)->exists()) {
            return back()->with('error', 'Un bordereau existe déjà pour cette demande.');
        }

        $path = null;
        if ($request->hasFile('fichier_joint')) {
            $path = $request->file('fichier_joint')->store('bordereaux-virements', 'public');
        }

        $bordereau = BordereauVirement::create([
            'demande_fonds_id' => $demande->id,
            'poste_id' => $demande->poste_id,
            'user_id' => Auth::id(),
            'mois' => $demande->mois,
            'annee' => $demande->annee,
            'periode_debut' => $data['periode_debut'] ?? null,
            'periode_fin' => $data['periode_fin'] ?? null,
            'status' => 'brouillon',
            'fichier_joint' => $path,
        ]);

        return redirect()
            ->route('virements.poste.show', $bordereau)
            ->with('success', 'Bordereau créé. Ajoutez maintenant les VIR (1 banque = 1 VIR).');
    }

    public function show(BordereauVirement $bordereau)
    {
        $this->authorizePoste($bordereau->poste_id);
        $bordereau->load(['demandeFonds', 'poste', 'virements.banque', 'virements.lignes']);

        return view('virements.poste.show', [
            'bordereau' => $bordereau,
            'banques' => Banque::actives()->orderBy('nom')->get(),
        ]);
    }

    public function storeVirement(Request $request, BordereauVirement $bordereau)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);

        $request->merge([
            'montant' => str_replace(' ', '', (string) $request->input('montant')),
        ]);

        $data = $request->validate([
            'banque_id' => 'required|exists:banques,id',
            'numero_vir' => 'required|string|max:100',
            'montant' => 'required|numeric|min:1',
            'observation' => 'nullable|string|max:1000',
            'fichier_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($bordereau->virements()->where('banque_id', $data['banque_id'])->exists()) {
            return back()->with('error', 'Un VIR existe déjà pour cette banque sur ce bordereau.');
        }

        $msg = 'VIR créé.';

        DB::transaction(function () use ($request, $bordereau, $data, &$msg) {
            $virement = $bordereau->virements()->create([
                'poste_id' => $bordereau->poste_id,
                'banque_id' => $data['banque_id'],
                'numero_vir' => trim($data['numero_vir']),
                'mois' => $bordereau->mois,
                'annee' => $bordereau->annee,
                'montant' => $data['montant'],
                'statut_paiement' => 'non_paye',
                'observation' => $data['observation'] ?? null,
            ]);

            $lignes = ExcelVirementService::parseDetailFile($request->file('fichier_excel')->getRealPath());
            if ($lignes === []) {
                throw ValidationException::withMessages([
                    'fichier_excel' => 'Aucune ligne valide trouvée dans le fichier Excel.',
                ]);
            }

            $virement->load('banque');
            foreach ($lignes as $ligne) {
                $virement->lignes()->create([
                    'num_be' => $ligne['num_be'],
                    'num_mdt' => $ligne['num_mdt'],
                    'ord' => $ligne['ord'],
                    'programme' => $ligne['programme'],
                    'creancier_libelle' => $ligne['creancier'] ?? ($virement->banque->nom ?? null),
                    'num_compte' => $ligne['num_compte'] ?? 'NEANT',
                    'montant' => $ligne['montant'],
                ]);
            }

            $somme = $virement->sommeLignes();
            $msg = 'VIR créé avec '.count($lignes).' ligne(s) détail importée(s).';
            if (abs($somme - (float) $virement->montant) > 0.5) {
                $msg .= ' Attention : total détail ('.number_format($somme, 0, ',', ' ').') ≠ montant VIR ('.number_format((float) $virement->montant, 0, ',', ' ').').';
            }
        });

        return back()->with('success', $msg);
    }

    public function updateVirement(Request $request, BordereauVirement $bordereau, Virement $virement)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);
        $this->ensureVirementBelongs($bordereau, $virement);

        $request->merge([
            'montant' => str_replace(' ', '', (string) $request->input('montant')),
        ]);

        $data = $request->validate([
            'numero_vir' => 'required|string|max:100',
            'montant' => 'required|numeric|min:1',
            'observation' => 'nullable|string|max:1000',
        ]);

        $virement->update([
            'numero_vir' => trim($data['numero_vir']),
            'montant' => $data['montant'],
            'observation' => $data['observation'] ?? null,
        ]);

        return back()->with('success', 'VIR mis à jour.');
    }

    public function destroyVirement(BordereauVirement $bordereau, Virement $virement)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);
        $this->ensureVirementBelongs($bordereau, $virement);
        $virement->delete();

        return back()->with('success', 'VIR supprimé.');
    }

    public function storeLigne(Request $request, BordereauVirement $bordereau, Virement $virement)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);
        $this->ensureVirementBelongs($bordereau, $virement);

        $request->merge([
            'montant' => str_replace(' ', '', (string) $request->input('montant')),
        ]);

        $data = $request->validate([
            'num_be' => 'nullable|string|max:50',
            'num_mdt' => 'nullable|string|max:50',
            'ord' => 'nullable|string|max:50',
            'programme' => 'nullable|string|max:50',
            'creancier_libelle' => 'nullable|string|max:255',
            'num_compte' => 'nullable|string|max:100',
            'montant' => 'required|numeric|min:1',
        ]);

        $virement->lignes()->create([
            'num_be' => $data['num_be'] ?? null,
            'num_mdt' => $data['num_mdt'] ?? null,
            'ord' => $data['ord'] ?? null,
            'programme' => $data['programme'] ?? null,
            'creancier_libelle' => $data['creancier_libelle'] ?? ($virement->banque->nom ?? null),
            'num_compte' => $data['num_compte'] ?? 'NEANT',
            'montant' => $data['montant'],
        ]);

        return back()->with('success', 'Ligne détail ajoutée.');
    }

    public function destroyLigne(BordereauVirement $bordereau, Virement $virement, LigneVirement $ligne)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);
        $this->ensureVirementBelongs($bordereau, $virement);
        abort_unless($ligne->virement_id === $virement->id, 404);
        $ligne->delete();

        return back()->with('success', 'Ligne supprimée.');
    }

    public function importLignes(Request $request, BordereauVirement $bordereau, Virement $virement)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);
        $this->ensureVirementBelongs($bordereau, $virement);

        $request->validate([
            'fichier_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $lignes = ExcelVirementService::parseDetailFile($request->file('fichier_excel')->getRealPath());
        if ($lignes === []) {
            return back()->with('error', 'Aucune ligne valide trouvée dans le fichier Excel.');
        }

        DB::transaction(function () use ($virement, $lignes) {
            foreach ($lignes as $ligne) {
                $virement->lignes()->create([
                    'num_be' => $ligne['num_be'],
                    'num_mdt' => $ligne['num_mdt'],
                    'ord' => $ligne['ord'],
                    'programme' => $ligne['programme'],
                    'creancier_libelle' => $ligne['creancier'] ?? ($virement->banque->nom ?? null),
                    'num_compte' => $ligne['num_compte'] ?? 'NEANT',
                    'montant' => $ligne['montant'],
                ]);
            }
        });

        $somme = $virement->sommeLignes();
        $msg = count($lignes).' ligne(s) importée(s). Somme détail : '.number_format($somme, 0, ',', ' ').' FCFA';
        if (abs($somme - (float) $virement->montant) > 0.5) {
            $msg .= ' — attention : différent du montant VIR ('.number_format((float) $virement->montant, 0, ',', ' ').').';
        }

        return back()->with('success', $msg);
    }

    public function templateExcel()
    {
        return ExcelVirementService::templateDetailResponse();
    }

    public function pdf(BordereauVirement $bordereau)
    {
        $this->authorizePoste($bordereau->poste_id);
        $bordereau->load(['demandeFonds', 'poste', 'virements.banque', 'virements.lignes']);

        $poste = $bordereau->poste;
        $intituleOfficiel = $poste?->intitulePourEntete() ?? 'AGENCE COMPTABLE CENTRALE DU TRÉSOR';
        $regionLibelle = $poste?->regionPourEntete();
        $posteNom = $poste?->libelleAffiche() ?? ($poste->nom ?? '');

        $pdf = Pdf::loadView('virements.pdf.bordereau', compact(
            'bordereau',
            'intituleOfficiel',
            'regionLibelle',
            'posteNom'
        ))->setPaper('a4', 'landscape');

        $suffixe = preg_replace('/\s+/', '_', (string) ($posteNom ?: 'poste'));
        $filename = "Bordereau_VIR_{$suffixe}_{$bordereau->mois}_{$bordereau->annee}.pdf";

        return $pdf->download($filename);
    }

    public function pdfVirement(BordereauVirement $bordereau, Virement $virement)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureVirementBelongs($bordereau, $virement);
        $virement->load(['poste', 'banque', 'lignes', 'payePar']);

        $poste = $virement->poste ?? $bordereau->poste;
        $intituleOfficiel = $poste?->intitulePourEntete() ?? 'AGENCE COMPTABLE CENTRALE DU TRÉSOR';
        $regionLibelle = $poste?->regionPourEntete();
        $posteNom = $poste?->libelleAffiche() ?? ($poste->nom ?? '');
        $signatureLibelle = 'Le Responsable du Poste comptable';

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

    public function soumettre(BordereauVirement $bordereau)
    {
        $this->authorizePoste($bordereau->poste_id);
        $this->ensureBrouillon($bordereau);

        if ($bordereau->virements()->count() === 0) {
            return back()->with('error', 'Ajoutez au moins un VIR avant de soumettre.');
        }

        $incomplets = $bordereau->virements()
            ->where(function ($q) {
                $q->whereNull('numero_vir')->orWhere('numero_vir', '')->orWhere('montant', '<=', 0);
            })->count();
        if ($incomplets > 0) {
            return back()->with('error', 'Chaque VIR doit avoir un n° VIR et un montant.');
        }

        $bordereau->update([
            'status' => 'soumis',
            'soumis_at' => now(),
        ]);

        $bordereauFresh = $bordereau->fresh(['poste', 'virements']);
        User::whereIn('role', ['acct', 'admin'])->where('active', true)->get()
            ->each(fn ($u) => $u->notify(new BordereauVirementSoumis($bordereauFresh)));
        MailAlerteService::notifyEvenement('vir_soumission', new BordereauVirementSoumis($bordereauFresh));

        return redirect()
            ->route('virements.poste.show', $bordereau)
            ->with('success', 'Bordereau soumis à l’ACCT.');
    }

    private function findDemandeApprouvee(int $id): DemandeFonds
    {
        $demande = DemandeFonds::with('poste')->findOrFail($id);
        abort_unless($demande->status === 'approuve', 422, 'La demande doit être approuvée.');

        return $demande;
    }

    private function authorizePoste(?int $posteId): void
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['admin', 'acct'])) {
            return;
        }
        abort_unless($user->hasRole('tresorier') && (int) $user->poste_id === (int) $posteId, 403);
    }

    private function ensureBrouillon(BordereauVirement $bordereau): void
    {
        abort_unless($bordereau->isBrouillon(), 422, 'Bordereau déjà soumis.');
    }

    private function ensureVirementBelongs(BordereauVirement $bordereau, Virement $virement): void
    {
        abort_unless((int) $virement->bordereau_virement_id === (int) $bordereau->id, 404);
    }
}

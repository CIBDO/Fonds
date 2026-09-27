<?php

namespace App\Http\Controllers\FNL;

use App\Http\Controllers\Controller;
use App\Models\PaiementFnl;
use App\Models\Poste;
use App\Models\User;
use App\Notifications\FnlPaiementSoumis;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class PaiementFnlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $this->utilisateurAutorise();

        $query = PaiementFnl::with(['poste', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc');

        if (!$this->estValidateur($user)) {
            $query->where('poste_id', $user->poste_id);
        } elseif ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $paiements = $query->paginate(20)->appends($request->query());
        $postes = $this->estValidateur($user)
            ? Poste::orderBy('nom')->get()
            : Poste::where('id', $user->poste_id)->get();

        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);

        return view('fnl.paiements.index', compact('paiements', 'postes', 'moisList', 'annees'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('tresorier') || !$user->poste_id) {
            abort(403, 'Seul un trésorier rattaché à un poste peut saisir un paiement FNL.');
        }

        $poste = Poste::findOrFail($user->poste_id);
        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);

        return view('fnl.paiements.create', compact('poste', 'moisList', 'annees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('tresorier') || !$user->poste_id) {
            abort(403, 'Seul un trésorier rattaché à un poste peut saisir un paiement FNL.');
        }

        $validated = $this->validerSaisie($request);
        $validated['poste_id'] = $user->poste_id;

        $existe = PaiementFnl::where('poste_id', $validated['poste_id'])
            ->where('mois', $validated['mois'])
            ->where('annee', $validated['annee'])
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'mois' => 'Un paiement FNL existe déjà pour ce poste et cette période.',
            ]);
        }

        $paiement = PaiementFnl::create([
            'poste_id' => $validated['poste_id'],
            'mois' => $validated['mois'],
            'annee' => $validated['annee'],
            'retenue_fnl' => $validated['retenue_fnl'],
            'reference_paiement' => $validated['reference_paiement'] ?? null,
            'date_paiement' => $validated['date_paiement'] ?? null,
            'observation' => $validated['observation'] ?? null,
            'statut' => 'soumis',
            'date_saisie' => now(),
            'saisi_par' => $user->id,
        ]);

        $this->enregistrerPreuve($request, $paiement);
        $this->notifierAccd($paiement);

        Alert::success('Succès', 'Paiement FNL soumis à l\'ACCD.');

        return redirect()->route('fnl.paiements.index');
    }

    public function show(PaiementFnl $paiement)
    {
        $this->autoriserConsultation($paiement);
        $paiement->load(['poste', 'saisiPar', 'validePar']);

        return view('fnl.paiements.show', [
            'paiement' => $paiement,
            'peutModifier' => $this->peutModifier($paiement),
            'peutValider' => $this->estValidateur(Auth::user()) && $paiement->statut === 'soumis',
        ]);
    }

    public function edit(PaiementFnl $paiement)
    {
        $this->autoriserModification($paiement);
        $paiement->load('poste');
        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);

        return view('fnl.paiements.edit', compact('paiement', 'moisList', 'annees'));
    }

    public function update(Request $request, PaiementFnl $paiement)
    {
        $this->autoriserModification($paiement);
        $validated = $this->validerSaisie($request, $paiement);

        $paiement->update([
            'mois' => $validated['mois'],
            'annee' => $validated['annee'],
            'retenue_fnl' => $validated['retenue_fnl'],
            'reference_paiement' => $validated['reference_paiement'] ?? null,
            'date_paiement' => $validated['date_paiement'] ?? null,
            'observation' => $validated['observation'] ?? null,
            'statut' => 'soumis',
            'motif_rejet' => null,
            'date_validation' => null,
            'valide_par' => null,
            'date_saisie' => now(),
        ]);

        $this->enregistrerPreuve($request, $paiement);
        $this->notifierAccd($paiement);

        Alert::success('Succès', 'Paiement FNL renvoyé à l\'ACCD.');

        return redirect()->route('fnl.paiements.show', $paiement);
    }

    public function valider(PaiementFnl $paiement)
    {
        $user = Auth::user();
        if (!$this->estValidateur($user)) {
            abort(403);
        }
        if ($paiement->statut !== 'soumis') {
            Alert::error('Erreur', 'Seuls les paiements soumis peuvent être validés.');
            return redirect()->back();
        }

        $paiement->update([
            'statut' => 'valide',
            'date_validation' => now(),
            'valide_par' => $user->id,
            'motif_rejet' => null,
        ]);

        Alert::success('Succès', 'Paiement FNL validé.');

        return redirect()->back();
    }

    public function rejeter(Request $request, PaiementFnl $paiement)
    {
        $user = Auth::user();
        if (!$this->estValidateur($user)) {
            abort(403);
        }
        if ($paiement->statut !== 'soumis') {
            Alert::error('Erreur', 'Seuls les paiements soumis peuvent être rejetés.');
            return redirect()->back();
        }

        $validated = $request->validate([
            'motif_rejet' => 'required|string|max:2000',
        ]);

        $paiement->update([
            'statut' => 'rejete',
            'motif_rejet' => $validated['motif_rejet'],
            'date_validation' => now(),
            'valide_par' => $user->id,
        ]);

        Alert::success('Succès', 'Paiement FNL rejeté.');

        return redirect()->back();
    }

    public function preuve(PaiementFnl $paiement)
    {
        $this->autoriserConsultation($paiement);

        if (!$paiement->preuve_paiement || !Storage::disk('public')->exists($paiement->preuve_paiement)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $paiement->preuve_paiement,
            basename($paiement->preuve_paiement)
        );
    }

    public function situationMensuelle(Request $request)
    {
        $user = $this->utilisateurAutorise();

        $moisList = PaiementFnl::moisList();
        $mois = $request->filled('mois') ? (int) $request->get('mois') : null;
        $annee = $request->filled('annee') ? (int) $request->get('annee') : null;
        if ($mois !== null && ($mois < 1 || $mois > 12)) {
            $mois = null;
        }
        $nomMois = $mois ? ($moisList[$mois] ?? (string) $mois) : null;

        $query = PaiementFnl::with('poste');
        if ($annee) {
            $query->where('annee', $annee);
        }
        if ($mois) {
            $query->where('mois', $mois);
        }

        $posteNom = null;
        $statutsAutorises = ['soumis', 'valide', 'rejete'];
        $statutFiltre = in_array($request->input('statut'), $statutsAutorises, true)
            ? $request->input('statut')
            : null;
        if ($statutFiltre) {
            $query->where('statut', $statutFiltre);
        }
        $afficherStatut = $statutFiltre === null;

        if ($this->estValidateur($user)) {
            if ($request->filled('poste_id')) {
                $poste = Poste::findOrFail($request->integer('poste_id'));
                $query->where('poste_id', $poste->id);
                $posteNom = $poste->nom;
            }
        } else {
            $query->where('poste_id', $user->poste_id);
            $posteNom = $user->poste->nom ?? 'Poste';
        }

        $paiements = $query->get()->sortBy(fn ($paiement) => sprintf(
            '%04d-%02d-%s',
            $paiement->annee,
            $paiement->mois,
            $paiement->poste->nom ?? ''
        ));

        $totalRetenue = $paiements->sum('retenue_fnl');
        if ($nomMois && $annee) {
            $periodeLibelle = 'DU MOIS DE '.mb_strtoupper($nomMois).' '.$annee;
        } elseif ($annee) {
            $periodeLibelle = "DE L'ANNEE ".$annee;
        } elseif ($nomMois) {
            $periodeLibelle = 'DU MOIS DE '.mb_strtoupper($nomMois);
        } else {
            $periodeLibelle = 'TOUTES PERIODES';
        }

        $pdf = PDF::loadView('fnl.pdf.situation-mensuelle', compact(
            'paiements',
            'mois',
            'annee',
            'nomMois',
            'periodeLibelle',
            'totalRetenue',
            'posteNom',
            'afficherStatut'
        ));
        $pdf->setPaper('A4', 'landscape');

        $suffixePoste = $posteNom ? '_'.preg_replace('/\s+/', '_', $posteNom) : '';
        $suffixePeriode = $nomMois && $annee
            ? "_{$nomMois}_{$annee}"
            : ($annee ? "_{$annee}" : '');

        return $pdf->download("Situation_Paiements_FNL{$suffixePoste}{$suffixePeriode}.pdf");
    }

    private function utilisateurAutorise()
    {
        $user = Auth::user();
        if ($this->estValidateur($user)) {
            return $user;
        }
        if ($user->hasRole('tresorier') && $user->poste_id) {
            return $user;
        }

        abort(403, 'Vous n\'avez pas accès aux paiements FNL.');
    }

    private function estValidateur($user): bool
    {
        return $user->hasAnyRole(['accd', 'admin']);
    }

    private function autoriserConsultation(PaiementFnl $paiement): void
    {
        $user = Auth::user();
        if ($this->estValidateur($user)) {
            return;
        }
        if ($user->hasRole('tresorier') && (int) $user->poste_id === (int) $paiement->poste_id) {
            return;
        }

        abort(403);
    }

    private function peutModifier(PaiementFnl $paiement): bool
    {
        $user = Auth::user();

        return $user->hasRole('tresorier')
            && (int) $user->poste_id === (int) $paiement->poste_id
            && in_array($paiement->statut, ['soumis', 'rejete'], true);
    }

    private function autoriserModification(PaiementFnl $paiement): void
    {
        if (!$this->peutModifier($paiement)) {
            abort(403, 'Ce paiement ne peut plus être modifié.');
        }
    }

    private function validerSaisie(Request $request, ?PaiementFnl $paiement = null): array
    {
        $retenue = str_replace(["\u{00A0}", ' '], '', (string) $request->input('retenue_fnl'));
        $request->merge([
            'retenue_fnl' => str_replace(',', '.', $retenue),
        ]);

        $validated = $request->validate([
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020',
            'retenue_fnl' => 'required|numeric|min:0',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'nullable|date',
            'observation' => 'nullable|string',
            'preuve_paiement' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $posteId = $paiement?->poste_id ?? Auth::user()->poste_id;
        $doublon = PaiementFnl::where('poste_id', $posteId)
            ->where('mois', $validated['mois'])
            ->where('annee', $validated['annee'])
            ->when($paiement, fn ($query) => $query->where('id', '!=', $paiement->id))
            ->exists();

        if ($doublon) {
            throw ValidationException::withMessages([
                'mois' => 'Un paiement FNL existe déjà pour ce poste et cette période.',
            ]);
        }

        return $validated;
    }

    private function enregistrerPreuve(Request $request, PaiementFnl $paiement): void
    {
        if (!$request->hasFile('preuve_paiement')) {
            return;
        }

        if ($paiement->preuve_paiement && Storage::disk('public')->exists($paiement->preuve_paiement)) {
            Storage::disk('public')->delete($paiement->preuve_paiement);
        }

        $path = $request->file('preuve_paiement')->store("preuves-fnl/paiements/{$paiement->id}", 'public');
        $paiement->update(['preuve_paiement' => $path]);
    }

    private function notifierAccd(PaiementFnl $paiement): void
    {
        $paiement->load('poste');
        $destinataires = User::where('role', 'accd')->where('active', true)->get();

        foreach ($destinataires as $destinataire) {
            $destinataire->notify(new FnlPaiementSoumis($paiement));
        }
    }
}

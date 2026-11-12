<?php

namespace App\Http\Controllers\FNL;

use App\Http\Controllers\Controller;
use App\Models\PaiementFnl;
use App\Models\Poste;
use App\Models\User;
use App\Notifications\FnlPaiementSoumis;
use App\Services\MailAlerteService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaiementFnlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $this->utilisateurAutorise();

        $query = PaiementFnl::with(['poste.parent', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc');

        if (!$this->estValidateur($user)) {
            $query->whereIn('poste_id', $this->posteIdsAccessibles($user));
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
        $postes = $this->postesPourFiltres($user);
        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);
        $reconstitution = $this->reconstitutionIndex($request, $user, $moisList);

        return view('fnl.paiements.index', compact('paiements', 'postes', 'moisList', 'annees', 'reconstitution'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('tresorier') || !$user->poste_id) {
            abort(403, 'Seul un trésorier rattaché à un poste peut saisir un paiement FNL.');
        }

        $poste = Poste::with('recettesPerceptions')->findOrFail($user->poste_id);
        $cibles = $this->ciblesSaisie($poste);
        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);

        return view('fnl.paiements.create', compact('poste', 'cibles', 'moisList', 'annees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('tresorier') || !$user->poste_id) {
            abort(403, 'Seul un trésorier rattaché à un poste peut saisir un paiement FNL.');
        }

        $posteUser = Poste::with('recettesPerceptions')->findOrFail($user->poste_id);
        $validated = $this->validerSaisie($request, $posteUser);
        $posteCible = Poste::findOrFail($validated['poste_id']);

        $this->asserterDroitSaisieSurPoste($user, $posteUser, $posteCible);
        $this->asserterPrioriteRp($user, $posteUser, $posteCible, $validated['mois'], $validated['annee']);

        $paiement = PaiementFnl::create([
            'poste_id' => $posteCible->id,
            'libelle' => $validated['libelle'],
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
        $paiement->load(['poste.parent', 'saisiPar', 'validePar']);

        return view('fnl.paiements.show', [
            'paiement' => $paiement,
            'peutModifier' => $this->peutModifier($paiement),
            'peutValider' => $this->estValidateur(Auth::user()) && $paiement->statut === 'soumis',
        ]);
    }

    public function edit(PaiementFnl $paiement)
    {
        $this->autoriserModification($paiement);
        $paiement->load('poste.parent');
        $user = Auth::user();
        $posteUser = Poste::with('recettesPerceptions')->findOrFail($user->poste_id);
        $cibles = $this->ciblesSaisie($posteUser);
        $moisList = PaiementFnl::moisList();
        $annees = range((int) date('Y'), (int) date('Y') - 5);

        return view('fnl.paiements.edit', compact('paiement', 'posteUser', 'cibles', 'moisList', 'annees'));
    }

    public function update(Request $request, PaiementFnl $paiement)
    {
        $this->autoriserModification($paiement);
        $user = Auth::user();
        $posteUser = Poste::with('recettesPerceptions')->findOrFail($user->poste_id);
        $validated = $this->validerSaisie($request, $posteUser, $paiement);
        $posteCible = Poste::findOrFail($validated['poste_id']);

        $this->asserterDroitSaisieSurPoste($user, $posteUser, $posteCible);
        $this->asserterPrioriteRp($user, $posteUser, $posteCible, $validated['mois'], $validated['annee'], $paiement);

        $paiement->update([
            'poste_id' => $posteCible->id,
            'libelle' => $validated['libelle'],
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
            'saisi_par' => $user->id,
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

        if (!$request->filled('annee')) {
            Alert::warning('Attention', 'Sélectionnez au moins l\'année pour générer l\'état FNL.');

            return redirect()->route('fnl.paiements.index', $request->only(['poste_id', 'mois', 'annee', 'statut']));
        }

        $annee = (int) $request->get('annee');
        $mois = $request->filled('mois') ? (int) $request->get('mois') : null;
        if ($annee < 2020 || ($mois !== null && ($mois < 1 || $mois > 12))) {
            Alert::warning('Attention', 'Période invalide pour l\'état FNL.');

            return redirect()->route('fnl.paiements.index', $request->only(['poste_id', 'mois', 'annee', 'statut']));
        }

        $nomMois = $mois !== null ? ($moisList[$mois] ?? (string) $mois) : 'Tous les mois';
        $periodeLibelle = $mois !== null
            ? mb_strtoupper($nomMois).' '.$annee
            : 'ANNÉE '.$annee;

        // Les états officiels ne reprennent que les paiements validés
        $statutFiltre = 'valide';
        $estGlobal = $this->estValidateur($user) && !$request->filled('poste_id');
        $posteUser = $user->poste_id ? Poste::with('recettesPerceptions')->find($user->poste_id) : null;
        $estRp = !$estGlobal && $posteUser && $posteUser->isRp();

        $query = PaiementFnl::with(['poste.parent', 'saisiPar'])
            ->where('annee', $annee)
            ->where('statut', 'valide');
        if ($mois !== null) {
            $query->where('mois', $mois);
        }

        $afficherPeriode = $mois === null;

        if ($estGlobal) {
            $paiements = $query->orderBy('mois')->orderBy('poste_id')->get();
            $lignes = $this->construireLignesEtatGlobal($paiements, $afficherPeriode);
            $poste = null;
            $intituleOfficiel = 'DIRECTION GÉNÉRALE DU TRÉSOR ET DE LA COMPTABILITÉ PUBLIQUE';
            $regionLibelle = 'Situation globale — tous les postes';
            $posteNom = 'Consolidation FNL';
        } else {
            $poste = $this->resoudrePosteSituation($request, $user, $estRp);
            $poste->loadMissing('recettesPerceptions', 'parent');

            if ($estRp) {
                $posteIds = collect([(int) $posteUser->id]);
                $lignesScope = 'rp';
            } else {
                $posteIds = collect([(int) $poste->id])
                    ->merge($poste->recettesPerceptions->pluck('id')->map(fn ($id) => (int) $id))
                    ->unique()
                    ->values();
                $lignesScope = 'tr';
            }

            $paiements = $query->whereIn('poste_id', $posteIds->all())->orderBy('mois')->get();
            $lignes = $lignesScope === 'rp'
                ? $this->construireLignesEtatRp($posteUser, $paiements, $afficherPeriode)
                : $this->construireLignesEtat($poste, $paiements, $afficherPeriode);

            $intituleOfficiel = $estRp ? $posteUser->libelleAffiche() : $poste->intitulePourEntete();
            $regionLibelle = $estRp ? $posteUser->regionPourEntete() : $poste->regionPourEntete();
            $posteNom = $estRp ? $posteUser->libelleAffiche() : $poste->nom;
            if ($estRp) {
                $poste = $posteUser;
            }
        }

        $totalRetenue = collect($lignes)->where('type', '!=', 'section')->sum(fn ($l) => (float) ($l['montant'] ?? 0));
        $afficherStatut = false;
        $filtresActifs = [
            'annee' => $annee,
            'mois' => $mois,
            'poste_id' => $request->input('poste_id'),
            'global' => $estGlobal,
        ];

        if ($request->boolean('preview')) {
            return view('fnl.paiements.situation-preview', compact(
                'poste',
                'lignes',
                'totalRetenue',
                'periodeLibelle',
                'intituleOfficiel',
                'regionLibelle',
                'mois',
                'annee',
                'nomMois',
                'statutFiltre',
                'estGlobal',
                'filtresActifs'
            ));
        }

        $pdf = PDF::loadView('fnl.pdf.situation-mensuelle', compact(
            'lignes',
            'mois',
            'annee',
            'nomMois',
            'periodeLibelle',
            'totalRetenue',
            'posteNom',
            'intituleOfficiel',
            'regionLibelle',
            'afficherStatut',
            'estGlobal'
        ));
        $pdf->setPaper('A4', 'landscape');

        $suffixePoste = '_'.preg_replace('/\s+/', '_', $posteNom);
        $suffixePeriode = $mois !== null ? "_{$nomMois}_{$annee}" : "_{$annee}";

        return $pdf->download("Tableau_Paiement_FNL{$suffixePoste}{$suffixePeriode}.pdf");
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $user = $this->utilisateurAutorise();
        if (!$this->estValidateur($user)) {
            abort(403, 'Seul l\'ACCD ou un administrateur peut exporter la consolidation FNL.');
        }

        $query = PaiementFnl::with(['poste.parent', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->orderBy('poste_id');

        if ($request->filled('poste_id')) {
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

        $paiements = $query->get();
        $filename = 'consolidation_fnl_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($paiements) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, [
                'Poste supérieur',
                'Poste / RP',
                'Type',
                'Libellé',
                'Mois',
                'Année',
                'Montant FCFA',
                'Référence paiement',
                'Date paiement',
                'Observation',
                'Statut',
                'Saisi par',
                'Date saisie',
            ], ';');

            $total = 0;
            foreach ($paiements as $paiement) {
                $poste = $paiement->poste;
                $superieur = $poste?->isRp() ? ($poste->parent->nom ?? '') : ($poste->nom ?? '');
                $type = $poste?->isRp() ? 'RP' : 'Poste supérieur';
                $total += (float) $paiement->retenue_fnl;

                fputcsv($out, [
                    $superieur,
                    $poste?->libelleAffiche() ?? '',
                    $type,
                    $paiement->libelle_affiche,
                    $paiement->nom_mois,
                    $paiement->annee,
                    number_format((float) $paiement->retenue_fnl, 0, ',', ' '),
                    $paiement->reference_paiement,
                    optional($paiement->date_paiement)->format('d/m/Y'),
                    $paiement->observation,
                    $paiement->statut,
                    $paiement->saisiPar->name ?? '',
                    optional($paiement->date_saisie)->format('d/m/Y H:i'),
                ], ';');
            }

            fputcsv($out, [
                '', '', '', 'TOTAL', '', '',
                number_format($total, 0, ',', ' '),
                '', '', '', '', '', '',
            ], ';');

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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

    private function posteIdsAccessibles($user): array
    {
        $poste = Poste::with('recettesPerceptions')->find($user->poste_id);
        if (!$poste) {
            return [(int) $user->poste_id];
        }

        if ($poste->isSuperieur()) {
            return collect([$poste->id])
                ->merge($poste->recettesPerceptions->pluck('id'))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        return [(int) $poste->id];
    }

    private function postesPourFiltres($user)
    {
        if ($this->estValidateur($user)) {
            return Poste::with('parent')->orderBy('type')->orderBy('nom')->get();
        }

        return Poste::with('parent')->whereIn('id', $this->posteIdsAccessibles($user))->orderBy('nom')->get();
    }

    /**
     * Total reconstitué (paiements validés) dès qu'une année est filtrée.
     * Mois / poste optionnels (mois vide = tous les mois).
     */
    private function reconstitutionIndex(Request $request, $user, array $moisList): ?array
    {
        if (!$request->filled('annee')) {
            return null;
        }

        $annee = (int) $request->annee;
        $mois = $request->filled('mois') ? (int) $request->mois : null;
        if ($annee < 2020 || ($mois !== null && ($mois < 1 || $mois > 12))) {
            return null;
        }

        $query = PaiementFnl::query()
            ->where('annee', $annee)
            ->where('statut', 'valide');
        if ($mois !== null) {
            $query->where('mois', $mois);
        }

        $posteFiltre = null;
        if (!$this->estValidateur($user)) {
            $query->whereIn('poste_id', $this->posteIdsAccessibles($user));
        } elseif ($request->filled('poste_id')) {
            $posteFiltre = Poste::with('parent')->find($request->integer('poste_id'));
            if ($posteFiltre) {
                $query->where('poste_id', $posteFiltre->id);
            }
        }

        $nb = (clone $query)->count();
        $montant = (float) (clone $query)->sum('retenue_fnl');
        $periode = $mois !== null
            ? ($moisList[$mois] ?? (string) $mois).' '.$annee
            : 'Année '.$annee.' (tous les mois)';

        return [
            'mois' => $mois,
            'annee' => $annee,
            'periode' => $periode,
            'montant' => $montant,
            'nb' => $nb,
            'poste' => $posteFiltre,
        ];
    }

    private function ciblesSaisie(Poste $posteUser)
    {
        if ($posteUser->isRp()) {
            return collect([$posteUser]);
        }

        return collect([$posteUser])->merge($posteUser->recettesPerceptions);
    }

    private function autoriserConsultation(PaiementFnl $paiement): void
    {
        $user = Auth::user();
        if ($this->estValidateur($user)) {
            return;
        }
        if ($user->hasRole('tresorier') && in_array((int) $paiement->poste_id, $this->posteIdsAccessibles($user), true)) {
            return;
        }

        abort(403);
    }

    private function peutModifier(PaiementFnl $paiement): bool
    {
        $user = Auth::user();
        if (!$user->hasRole('tresorier') || !$user->poste_id) {
            return false;
        }
        if (!in_array($paiement->statut, ['soumis', 'rejete'], true)) {
            return false;
        }

        $posteUser = Poste::find($user->poste_id);
        $paiement->loadMissing(['poste', 'saisiPar']);

        if (!$posteUser || !$paiement->poste) {
            return false;
        }

        // RP : priorité totale sur sa propre ligne
        if ($posteUser->isRp() && (int) $posteUser->id === (int) $paiement->poste_id) {
            return true;
        }

        // Poste supérieur : ses propres lignes
        if ($posteUser->isSuperieur() && (int) $posteUser->id === (int) $paiement->poste_id) {
            return true;
        }

        // Poste supérieur pour une RP enfant : uniquement si la RP n'a pas déjà saisi
        if ($posteUser->isSuperieur()
            && $paiement->poste->isRp()
            && (int) $paiement->poste->parent_id === (int) $posteUser->id
            && !$paiement->aEteSaisiParRp()) {
            return true;
        }

        return false;
    }

    private function autoriserModification(PaiementFnl $paiement): void
    {
        if (!$this->peutModifier($paiement)) {
            abort(403, 'Ce paiement ne peut plus être modifié (priorité à la RP ou statut définitif).');
        }
    }

    private function validerSaisie(Request $request, Poste $posteUser, ?PaiementFnl $paiement = null): array
    {
        $retenue = str_replace(["\u{00A0}", ' '], '', (string) $request->input('retenue_fnl'));
        $request->merge([
            'retenue_fnl' => str_replace(',', '.', $retenue),
        ]);

        $ciblesIds = $this->ciblesSaisie($posteUser)->pluck('id')->all();

        $validated = $request->validate([
            'poste_id' => ['required', 'integer', Rule::in($ciblesIds)],
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020',
            'retenue_fnl' => 'required|numeric|min:0',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'nullable|date',
            'observation' => 'nullable|string',
            'preuve_paiement' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $posteCible = Poste::findOrFail($validated['poste_id']);
        // Libellé = nom de l'entité (TR ou RP), sans Budget/Collectivités
        $validated['libelle'] = $posteCible->libelleAffiche();

        return $validated;
    }

    private function asserterDroitSaisieSurPoste($user, Poste $posteUser, Poste $posteCible): void
    {
        $ids = $this->ciblesSaisie($posteUser)->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (!in_array((int) $posteCible->id, $ids, true)) {
            abort(403, 'Vous ne pouvez pas saisir pour ce poste.');
        }
    }

    private function asserterPrioriteRp($user, Poste $posteUser, Poste $posteCible, int $mois, int $annee, ?PaiementFnl $actuel = null): void
    {
        // Si le saisisseur est le TR et la cible une RP : bloquer si la RP a déjà une saisie active
        if ($posteUser->isSuperieur() && $posteCible->isRp()) {
            $existant = PaiementFnl::with('saisiPar')
                ->where('poste_id', $posteCible->id)
                ->where('mois', $mois)
                ->where('annee', $annee)
                ->whereIn('statut', ['soumis', 'valide'])
                ->when($actuel, fn ($q) => $q->where('id', '!=', $actuel->id))
                ->first();

            if ($existant && $existant->aEteSaisiParRp()) {
                throw ValidationException::withMessages([
                    'poste_id' => 'Cette RP a déjà saisi pour cette période. Priorité à la Recette-Perception.',
                ]);
            }
        }
    }

    private function resoudrePosteSituation(Request $request, $user, bool $garderRp = false): Poste
    {
        $poste = $this->estValidateur($user)
            ? Poste::findOrFail($request->integer('poste_id'))
            : Poste::findOrFail($user->poste_id);

        // TR/ACCD : ancrer sur le poste supérieur pour consolider ses RP
        // RP : rester sur sa propre entité (ne pas remonter au TR)
        if (!$garderRp && $poste->isRp()) {
            $poste = $poste->parent ?: $poste;
        }

        return $poste;
    }

    private function construireLignesEtat(Poste $posteSuperieur, $paiements, bool $afficherPeriode = false): array
    {
        $lignes = [];
        $parPoste = collect($paiements)->groupBy(fn ($p) => (int) $p->poste_id);

        // Détail : une ligne par virement — uniquement les entités qui ont envoyé des données
        foreach ($this->lignesEtatDetaillees(
            $posteSuperieur->libelleAffiche(),
            $parPoste->get((int) $posteSuperieur->id, collect()),
            (int) $posteSuperieur->id,
            $afficherPeriode
        ) as $ligne) {
            $lignes[] = $ligne;
        }

        $lignesRp = [];
        foreach ($posteSuperieur->recettesPerceptions as $rp) {
            foreach ($this->lignesEtatDetaillees(
                $rp->libelleAffiche(),
                $parPoste->get((int) $rp->id, collect()),
                (int) $rp->id,
                $afficherPeriode
            ) as $ligne) {
                $lignesRp[] = $ligne;
            }
        }

        if ($lignesRp !== []) {
            $lignes[] = [
                'type' => 'section',
                'libelle' => 'Recettes-Perceptions',
                'montant' => null,
                'reference' => null,
                'observation' => null,
                'nb_virements' => 0,
                'paiement' => null,
                'paiements' => collect(),
                'poste_id' => null,
            ];
            foreach ($lignesRp as $ligne) {
                $lignes[] = $ligne;
            }
        }

        return $lignes;
    }

    private function construireLignesEtatRp(Poste $rp, $paiements, bool $afficherPeriode = false): array
    {
        $paiementsRp = collect($paiements)
            ->filter(fn ($p) => (int) $p->poste_id === (int) $rp->id)
            ->values();
        if ($paiementsRp->isEmpty()) {
            $paiementsRp = collect($paiements)->values();
        }

        return $this->lignesEtatDetaillees($rp->libelleAffiche(), $paiementsRp, (int) $rp->id, $afficherPeriode);
    }

    /**
     * Situation globale : un bloc par poste supérieur ayant des paiements validés.
     */
    private function construireLignesEtatGlobal($paiements, bool $afficherPeriode = false): array
    {
        $paiements = collect($paiements)->filter(fn ($p) => $p->poste);
        if ($paiements->isEmpty()) {
            return [];
        }

        $parRacine = $paiements->groupBy(function (PaiementFnl $paiement) {
            $poste = $paiement->poste;
            if ($poste->isRp()) {
                return (int) ($poste->parent_id ?? $poste->id);
            }

            return (int) $poste->id;
        });

        $lignes = [];
        $racineIds = $parRacine->keys()->map(fn ($id) => (int) $id)->filter()->values();
        $racines = Poste::with('recettesPerceptions')
            ->whereIn('id', $racineIds->all())
            ->orderBy('nom')
            ->get()
            ->keyBy('id');

        foreach ($racines as $racineId => $racine) {
            $groupe = $parRacine->get($racineId, collect());
            $lignesPoste = $this->construireLignesEtat($racine, $groupe, $afficherPeriode);
            if ($lignesPoste === []) {
                continue;
            }

            $lignes[] = [
                'type' => 'section',
                'libelle' => $racine->intitulePourEntete(),
                'montant' => null,
                'reference' => null,
                'observation' => null,
                'nb_virements' => 0,
                'paiement' => null,
                'paiements' => collect(),
                'poste_id' => (int) $racine->id,
            ];
            foreach ($lignesPoste as $ligne) {
                $lignes[] = $ligne;
            }
        }

        return $lignes;
    }

    /**
     * Une ligne d'état par virement (détail). Aucune ligne si pas de données.
     *
     * @return array<int, array<string, mixed>>
     */
    private function lignesEtatDetaillees(string $libelle, $paiements, ?int $posteId = null, bool $afficherPeriode = false): array
    {
        $collection = collect($paiements)
            ->sortBy([
                fn (PaiementFnl $p) => optional($p->date_paiement)->timestamp ?? PHP_INT_MAX,
                fn (PaiementFnl $p) => $p->id,
            ])
            ->values();

        if ($collection->isEmpty()) {
            return [];
        }

        return $collection->map(function (PaiementFnl $paiement) use ($libelle, $afficherPeriode) {
            $periode = $paiement->nom_mois.' '.$paiement->annee;

            return [
                'type' => 'ligne',
                'libelle' => $afficherPeriode ? $libelle.' — '.$periode : $libelle,
                'montant' => (float) $paiement->retenue_fnl,
                'reference' => $this->formaterReference($paiement),
                'observation' => trim((string) ($paiement->observation ?? '')),
                'periode' => $periode,
                'nb_virements' => 1,
                'paiement' => $paiement,
                'paiements' => collect([$paiement]),
                'poste_id' => (int) $paiement->poste_id,
            ];
        })->all();
    }

    private function formaterReference(?PaiementFnl $paiement): string
    {
        if (!$paiement) {
            return '';
        }

        $parts = array_filter([
            $paiement->reference_paiement,
            $paiement->date_paiement ? 'DU '.$paiement->date_paiement->format('d/m/Y') : null,
        ]);

        return implode(' ', $parts);
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

        MailAlerteService::notifyEvenement('fnl_soumise', new FnlPaiementSoumis($paiement));
    }
}

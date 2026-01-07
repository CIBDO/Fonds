<?php

namespace App\Http\Controllers\TRIE;

use App\Http\Controllers\Controller;
use App\Models\CotisationTrie;
use App\Models\BureauTrie;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Notifications\TrieCotisationSoumise;

class CotisationTrieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des cotisations
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = CotisationTrie::with(['poste', 'bureauTrie', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc');

        // Si l'utilisateur n'est pas ACCT/Admin, filtrer par son poste
        if (!in_array($user->role, ['acct', 'admin'])) {
            $query->where('poste_id', $user->poste_id);
        }

        // Filtres
        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        $cotisations = $query->paginate(20);

        // Données pour les filtres - Filtrer les postes selon le profil
        if (in_array($user->role, ['acct', 'admin'])) {
            // Admin/ACCT peuvent filtrer par tous les postes
            $postes = Poste::orderBy('nom')->get();
        } else {
            // Les autres utilisateurs ne voient que leur propre poste
            $postes = $user->poste_id
                ? Poste::where('id', $user->poste_id)->get()
                : collect();
        }

        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $annees = range(date('Y'), date('Y') - 5);

        return view('trie.cotisations.index', compact('cotisations', 'postes', 'moisList', 'annees'));
    }

    /**
     * Formulaire de création - Sélection poste et période
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Si l'utilisateur n'est pas ACCT/Admin, il peut seulement saisir pour son poste
        if (in_array($user->role, ['acct', 'admin'])) {
            $postes = Poste::orderBy('nom')->get();
        } else {
            // Vérifier que l'utilisateur a un poste associé
            if (!$user->poste_id) {
                Alert::error('Erreur', 'Vous n\'êtes pas associé à un poste. Veuillez contacter l\'administrateur.');
                return redirect()->route('trie.cotisations.index');
            }
            $postes = Poste::where('id', $user->poste_id)->get();
        }

        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $annees = range(date('Y'), date('Y') - 5);

        // Présélectionner le poste de l'utilisateur connecté par défaut
        $posteId = $request->get('poste_id');

        // Si aucun poste n'est sélectionné et que l'utilisateur n'est pas admin/acct
        if (!$posteId && !in_array($user->role, ['acct', 'admin'])) {
            $posteId = $user->poste_id;
        }

        $mois = $request->get('mois', date('n'));
        $annee = $request->get('annee', date('Y'));

        $bureaux = collect(); // Collection vide au lieu d'un tableau
        $poste = null;

        // Si un poste est sélectionné, récupérer ses bureaux
        if ($posteId) {
            $poste = Poste::findOrFail($posteId);
            $bureaux = BureauTrie::where('poste_id', $posteId)
                ->where('actif', true)
                ->orderBy('code_bureau')
                ->get();

            // Vérifier les cotisations existantes
            foreach ($bureaux as $bureau) {
                $cotisationExistante = CotisationTrie::where('bureau_trie_id', $bureau->id)
                    ->where('mois', $mois)
                    ->where('annee', $annee)
                    ->first();

                $bureau->cotisation_existante = $cotisationExistante;
            }
        }

        return view('trie.cotisations.create', compact(
            'postes',
            'moisList',
            'annees',
            'bureaux',
            'poste',
            'posteId',
            'mois',
            'annee'
        ));
    }

    /**
     * API: Récupérer les mois déjà renseignés pour un poste et une année
     */
    public function getMoisRenseignes(Request $request)
    {
        try {
            $posteId = $request->get('poste_id');
            $annee = $request->get('annee');

            if (!$posteId || !$annee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres manquants',
                    'mois_renseignes' => []
                ], 400);
            }

            // Vérifier que le poste existe
            $poste = Poste::find($posteId);
            if (!$poste) {
                return response()->json([
                    'success' => false,
                    'message' => 'Poste introuvable',
                    'mois_renseignes' => []
                ], 404);
            }

            // Récupérer tous les bureaux actifs du poste
            $bureaux = BureauTrie::where('poste_id', $posteId)
                ->where('actif', true)
                ->pluck('id')
                ->toArray();

            // Si aucun bureau, retourner un tableau vide (pas d'erreur)
            if (empty($bureaux)) {
                return response()->json([
                    'success' => true,
                    'mois_renseignes' => []
                ], 200);
            }

            // Récupérer les mois qui ont au moins une cotisation pour cette année
            $moisRenseignes = CotisationTrie::whereIn('bureau_trie_id', $bureaux)
                ->where('annee', $annee)
                ->distinct()
                ->pluck('mois')
                ->toArray();

            return response()->json([
                'success' => true,
                'mois_renseignes' => $moisRenseignes
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur dans getMoisRenseignes: ' . $e->getMessage(), [
                'poste_id' => $request->get('poste_id'),
                'annee' => $request->get('annee'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la récupération des données',
                'mois_renseignes' => []
            ], 500);
        }
    }

    /**
     * Enregistrer les cotisations
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $mode = $request->input('mode', 'normal');

        try {
            DB::beginTransaction();

            if ($mode === 'rattrapage') {
                // Mode rattrapage : plusieurs mois
                $this->storeRattrapage($request, $user);
            } else {
                // Mode normal : un seul mois
                $this->storeNormal($request, $user);
            }

            DB::commit();

            $message = $mode === 'rattrapage'
                ? 'Les cotisations ont été enregistrées pour tous les mois sélectionnés avec succès.'
                : 'Les cotisations ont été enregistrées avec succès.';

            Alert::success('Succès', $message);
            return redirect()->route('trie.cotisations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Enregistrer en mode normal (un seul mois)
     */
    private function storeNormal(Request $request, $user)
    {
        $validated = $request->validate([
            'poste_id' => 'required|exists:postes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer|min:2020',
            'bureaux' => 'required|array|min:1',
            'bureaux.*.bureau_trie_id' => 'required|exists:bureaux_trie,id',
            'bureaux.*.montant_cotisation_courante' => 'required|numeric|min:0',
            'bureaux.*.montant_apurement' => 'nullable|numeric|min:0',
            'bureaux.*.detail_apurement' => 'nullable|string',
            'bureaux.*.mode_paiement' => 'nullable|in:cheque,virement,especes,autre',
            'bureaux.*.reference_paiement' => 'nullable|string',
            'bureaux.*.date_paiement' => 'nullable|date',
            'bureaux.*.observation' => 'nullable|string',
        ]);

        $posteId = $validated['poste_id'];
        $mois = $validated['mois'];
        $annee = $validated['annee'];

        foreach ($validated['bureaux'] as $bureauData) {
            // Vérifier si une cotisation existe déjà
            $existante = CotisationTrie::where('bureau_trie_id', $bureauData['bureau_trie_id'])
                ->where('mois', $mois)
                ->where('annee', $annee)
                ->first();

            if ($existante) {
                throw new \Exception('Une cotisation existe déjà pour ce bureau et cette période.');
            }

            // Créer la cotisation directement validée
            $cotisation = CotisationTrie::create([
                'poste_id' => $posteId,
                'bureau_trie_id' => $bureauData['bureau_trie_id'],
                'mois' => $mois,
                'annee' => $annee,
                'montant_cotisation_courante' => $bureauData['montant_cotisation_courante'],
                'montant_apurement' => $bureauData['montant_apurement'] ?? 0,
                'detail_apurement' => $bureauData['detail_apurement'] ?? null,
                'mode_paiement' => $bureauData['mode_paiement'] ?? null,
                'reference_paiement' => $bureauData['reference_paiement'] ?? null,
                'date_paiement' => $bureauData['date_paiement'] ?? null,
                'observation' => $bureauData['observation'] ?? null,
                'statut' => 'valide',
                'date_saisie' => now(),
                'date_validation' => now(),
                'saisi_par' => $user->id,
                'valide_par' => $user->id,
            ]);

            // Charger les relations pour la notification
            $cotisation->load(['poste', 'bureauTrie']);

            // Envoyer notification à l'ACCT
            $this->envoyerNotificationACCT($cotisation);
        }
    }

    /**
     * Enregistrer en mode rattrapage (plusieurs mois)
     */
    private function storeRattrapage(Request $request, $user)
    {
        $validated = $request->validate([
            'poste_id' => 'required|exists:postes,id',
            'annee' => 'required|integer|min:2020',
            'mois_selectionnes' => 'required|array|min:1',
            'mois_selectionnes.*' => 'integer|min:1|max:12',
        ]);

        $posteId = $validated['poste_id'];
        $annee = $validated['annee'];
        $moisSelectionnes = $validated['mois_selectionnes'];

        // Récupérer tous les bureaux actifs du poste
        $bureaux = BureauTrie::where('poste_id', $posteId)
            ->where('actif', true)
            ->get();

        $nbCotisations = 0;

        foreach ($moisSelectionnes as $mois) {
            foreach ($bureaux as $bureau) {
                // Récupérer les données du formulaire pour ce mois et ce bureau
                $montantCotisation = $request->input("cotisation_{$mois}_{$bureau->id}_montant_cotisation", 0);
                $montantApurement = $request->input("cotisation_{$mois}_{$bureau->id}_montant_apurement", 0);
                $reference = $request->input("cotisation_{$mois}_{$bureau->id}_reference");
                $datePaiement = $request->input("cotisation_{$mois}_{$bureau->id}_date_paiement");

                // Ne créer que si au moins un montant est renseigné
                if ($montantCotisation > 0 || $montantApurement > 0) {
                    // Vérifier si une cotisation existe déjà
                    $existante = CotisationTrie::where('bureau_trie_id', $bureau->id)
                        ->where('mois', $mois)
                        ->where('annee', $annee)
                        ->first();

                    if ($existante) {
                        throw new \Exception("Une cotisation existe déjà pour le bureau {$bureau->nom_bureau} en {$this->moisNoms[$mois]} {$annee}.");
                    }

                    // Créer la cotisation directement validée
                    $cotisation = CotisationTrie::create([
                        'poste_id' => $posteId,
                        'bureau_trie_id' => $bureau->id,
                        'mois' => $mois,
                        'annee' => $annee,
                        'montant_cotisation_courante' => $montantCotisation,
                        'montant_apurement' => $montantApurement,
                        'detail_apurement' => $montantApurement > 0 ? "Rattrapage {$this->moisNoms[$mois]} {$annee}" : null,
                        'mode_paiement' => null,
                        'reference_paiement' => $reference,
                        'date_paiement' => $datePaiement,
                        'observation' => "Saisie en mode rattrapage multi-mois",
                        'statut' => 'valide',
                        'date_saisie' => now(),
                        'date_validation' => now(),
                        'saisi_par' => $user->id,
                        'valide_par' => $user->id,
                    ]);

                    // Charger les relations pour la notification
                    $cotisation->load(['poste', 'bureauTrie']);

                    // Envoyer notification à l'ACCT
                    $this->envoyerNotificationACCT($cotisation);

                    $nbCotisations++;
                }
            }
        }

        if ($nbCotisations === 0) {
            throw new \Exception('Aucune cotisation à enregistrer. Veuillez saisir au moins un montant.');
        }
    }

    /**
     * Envoyer notification à l'ACCT pour une cotisation
     */
    private function envoyerNotificationACCT($cotisation)
    {
        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Envoyer notification à chaque utilisateur ACCT
        foreach ($acctUsers as $acctUser) {
            $acctUser->notify(new TrieCotisationSoumise($cotisation));
        }
    }

    // Tableau des mois pour les messages
    private $moisNoms = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];

    /**
     * Afficher une cotisation
     */
    public function show(CotisationTrie $cotisation)
    {
        $cotisation->load(['poste', 'bureauTrie', 'saisiPar', 'validePar']);
        return view('trie.cotisations.show', compact('cotisation'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(CotisationTrie $cotisation)
    {
        $user = Auth::user();

        // Seul le créateur (poste émetteur) peut modifier la cotisation
        if ($cotisation->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs cotisations');
            return redirect()->route('trie.cotisations.show', $cotisation);
        }

        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        return view('trie.cotisations.edit', compact('cotisation', 'moisList'));
    }

    /**
     * Mettre à jour une cotisation
     */
    public function update(Request $request, CotisationTrie $cotisation)
    {
        $user = Auth::user();

        // Seul le créateur (poste émetteur) peut modifier la cotisation
        if ($cotisation->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs cotisations');
            return redirect()->route('trie.cotisations.show', $cotisation);
        }

        $validated = $request->validate([
            'montant_cotisation_courante' => 'required|numeric|min:0',
            'montant_apurement' => 'nullable|numeric|min:0',
            'detail_apurement' => 'nullable|string',
            'mode_paiement' => 'nullable|in:cheque,virement,especes,autre',
            'reference_paiement' => 'nullable|string',
            'date_paiement' => 'nullable|date',
            'observation' => 'nullable|string',
        ]);

        $cotisation->update($validated);

        Alert::success('Succès', 'La cotisation a été modifiée avec succès.');
        return redirect()->route('trie.cotisations.show', $cotisation);
    }

    /**
     * Supprimer une cotisation
     */
    public function destroy(CotisationTrie $cotisation)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur peut supprimer cette cotisation
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $cotisation->poste_id) {
                Alert::error('Erreur', 'Vous ne pouvez supprimer que les cotisations de votre propre poste.');
                return redirect()->back();
            }
        }

        $cotisation->delete();

        Alert::success('Succès', 'La cotisation a été supprimée avec succès.');
        return redirect()->route('trie.cotisations.index');
    }

    /**
     * Générer l'état consolidé des cotisations pour un poste émetteur
     */
    public function etatConsolidePosteEmetteur(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->poste_id) {
            Alert::error('Erreur', 'Aucun poste assigné à votre compte');
            return redirect()->route('trie.cotisations.index');
        }

        $annee = $request->get('annee', date('Y'));
        $poste = $user->poste;

        // Récupérer les cotisations du poste émetteur
        $cotisations = CotisationTrie::with(['bureauTrie'])
            ->where('poste_id', $poste->id)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Organiser les données par bureau et par mois
        $donneesParBureau = [];
        $totalMensuel = array_fill(1, 12, 0);
        $totalGeneral = 0;

        foreach ($cotisations as $cotisation) {
            $bureauNom = $cotisation->bureauTrie->nom_bureau;
            $mois = $cotisation->mois;

            if (!isset($donneesParBureau[$bureauNom])) {
                $donneesParBureau[$bureauNom] = [
                    'nom' => $bureauNom,
                    'code' => $cotisation->bureauTrie->code_bureau,
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            $donneesParBureau[$bureauNom]['mois'][$mois] += $cotisation->montant_total;
            $donneesParBureau[$bureauNom]['total'] += $cotisation->montant_total;
            $totalMensuel[$mois] += $cotisation->montant_total;
            $totalGeneral += $cotisation->montant_total;
        }

        // Trier par nom de bureau
        ksort($donneesParBureau);

        $pdf = PDF::loadView('trie.pdf.etat-consolide-poste-emetteur', compact(
            'donneesParBureau',
            'totalMensuel',
            'totalGeneral',
            'annee',
            'poste'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("Situation_Cotisations_TRIE_{$poste->nom}_{$annee}.pdf");
    }
}

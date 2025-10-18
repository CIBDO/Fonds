<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\DeclarationPcs;
use App\Models\BureauDouane;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Notifications\PcsDeclarationSoumise;
use App\Notifications\PcsDeclarationValidee;
use App\Notifications\PcsDeclarationRejetee;

class DeclarationPcsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des déclarations
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = DeclarationPcs::with(['poste', 'bureauDouane', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc');

        // Filtres
        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
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

        // Si l'utilisateur n'est pas valideur, voir uniquement ses déclarations
        if (!$user->peut_valider_pcs) {
            $posteId = $user->poste_id;
            $query->where(function ($q) use ($posteId) {
                $q->where('poste_id', $posteId)
                  ->orWhereHas('bureauDouane', function ($q2) use ($posteId) {
                      $q2->where('poste_rgd_id', $posteId);
                  });
            });
        }

        $declarations = $query->paginate(20);

        return view('pcs.declarations.index', compact('declarations'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $poste = $user->poste;

        if (!$poste) {
            Alert::error('Erreur', 'Aucun poste assigné à votre compte');
            return redirect()->back();
        }

        // Si c'est la RGD, récupérer les bureaux
        $bureaux = $poste->isRgd() ? $poste->bureauxDouanes()->actif()->get() : collect();

        $programmes = ['UEMOA', 'AES'];
        $mois = range(1, 12);
        $annee = date('Y');

        return view('pcs.declarations.create', compact('poste', 'bureaux', 'programmes', 'mois', 'annee'));
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $poste = $user->poste;

        if (!$poste) {
            Alert::error('Erreur', 'Aucun poste assigné');
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            // Si c'est la RGD, traiter toutes les déclarations
            if ($poste->isRgd()) {
                $this->storeDeclarationsRgd($request, $poste, $user);
            } else {
                // Poste normal
                $this->storeDeclarationNormale($request, $poste, $user);
            }

            DB::commit();

            // Envoyer notification si soumission
            if ($request->input('action') === 'soumettre') {
                $this->envoyerNotificationSoumission($poste, $request);
            }

            Alert::success('Succès', 'Déclaration(s) enregistrée(s) avec succès');
            return redirect()->route('pcs.declarations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Enregistrer déclarations RGD (multiple)
     */
    private function storeDeclarationsRgd(Request $request, $poste, $user)
    {
        $mois = $request->input('mois');
        $annee = $request->input('annee');

        // Pour chaque programme (UEMOA et AES)
        foreach (['UEMOA', 'AES'] as $programme) {
            // Déclaration RGD propre
            $recouvrementRgd = $request->input("rgd_{$programme}_recouvrement");
            $reversementRgd = $request->input("rgd_{$programme}_reversement");

            if ($recouvrementRgd || $reversementRgd) {
                DeclarationPcs::updateOrCreate(
                    [
                        'poste_id' => $poste->id,
                        'bureau_douane_id' => null,
                        'programme' => $programme,
                        'mois' => $mois,
                        'annee' => $annee,
                    ],
                    [
                        'montant_recouvrement' => $recouvrementRgd ?? 0,
                        'montant_reversement' => $reversementRgd ?? 0,
                        'observation' => $request->input("rgd_{$programme}_observation"),
                        'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
                        'date_saisie' => now(),
                        'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                        'saisi_par' => $user->id,
                    ]
                );
            }

            // Déclarations des bureaux
            $bureaux = $poste->bureauxDouanes()->actif()->get();
            foreach ($bureaux as $bureau) {
                $recouvrementBureau = $request->input("bureau_{$bureau->id}_{$programme}_recouvrement");
                $reversementBureau = $request->input("bureau_{$bureau->id}_{$programme}_reversement");

                if ($recouvrementBureau || $reversementBureau) {
                    DeclarationPcs::updateOrCreate(
                        [
                            'poste_id' => null,
                            'bureau_douane_id' => $bureau->id,
                            'programme' => $programme,
                            'mois' => $mois,
                            'annee' => $annee,
                        ],
                        [
                            'montant_recouvrement' => $recouvrementBureau ?? 0,
                            'montant_reversement' => $reversementBureau ?? 0,
                            'observation' => $request->input("bureau_{$bureau->id}_{$programme}_observation"),
                            'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
                            'date_saisie' => now(),
                            'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                            'saisi_par' => $user->id,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Enregistrer déclaration poste normal
     */
    private function storeDeclarationNormale(Request $request, $poste, $user)
    {
        $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2020',
            'UEMOA_recouvrement' => 'nullable|numeric|min:0',
            'UEMOA_reversement' => 'nullable|numeric|min:0',
            'AES_recouvrement' => 'nullable|numeric|min:0',
            'AES_reversement' => 'nullable|numeric|min:0',
        ]);

        foreach (['UEMOA', 'AES'] as $programme) {
            $recouvrement = $request->input("{$programme}_recouvrement");
            $reversement = $request->input("{$programme}_reversement");

            if ($recouvrement || $reversement) {
                DeclarationPcs::create([
                    'poste_id' => $poste->id,
                    'bureau_douane_id' => null,
                    'programme' => $programme,
                    'mois' => $request->mois,
                    'annee' => $request->annee,
                    'montant_recouvrement' => $recouvrement ?? 0,
                    'montant_reversement' => $reversement ?? 0,
                    'observation' => $request->input("{$programme}_observation"),
                    'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
                    'date_saisie' => now(),
                    'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                    'saisi_par' => $user->id,
                ]);
            }
        }
    }

    /**
     * Détail d'une déclaration
     */
    public function show(DeclarationPcs $declaration)
    {
        $declaration->load(['poste', 'bureauDouane', 'saisiPar', 'validePar', 'piecesJointes', 'historiqueStatuts.utilisateur']);
        return view('pcs.declarations.show', compact('declaration'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(DeclarationPcs $declaration)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que c'est bien l'utilisateur qui a créé la déclaration
        if ($declaration->saisi_par !== $user->id && !$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous ne pouvez pas modifier cette déclaration');
            return redirect()->back();
        }

        // Ne peut être modifiée que si en brouillon ou rejetée
        if (!in_array($declaration->statut, ['brouillon', 'rejete'])) {
            Alert::error('Erreur', 'Seules les déclarations en brouillon ou rejetées peuvent être modifiées');
            return redirect()->back();
        }

        return view('pcs.declarations.edit', compact('declaration'));
    }

    /**
     * Mise à jour d'une déclaration
     */
    public function update(Request $request, DeclarationPcs $declaration)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier les permissions
        if ($declaration->saisi_par !== $user->id && !$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous ne pouvez pas modifier cette déclaration');
            return redirect()->back();
        }

        // Vérifier le statut
        if (!in_array($declaration->statut, ['brouillon', 'rejete'])) {
            Alert::error('Erreur', 'Seules les déclarations en brouillon ou rejetées peuvent être modifiées');
            return redirect()->back();
        }

        // Validation
        $validated = $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2020',
            'montant_recouvrement' => 'nullable|numeric|min:0',
            'montant_reversement' => 'nullable|numeric|min:0',
            'observation' => 'nullable|string',
        ]);

        // Mise à jour
        $declaration->update([
            'mois' => $validated['mois'],
            'annee' => $validated['annee'],
            'montant_recouvrement' => $validated['montant_recouvrement'] ?? 0,
            'montant_reversement' => $validated['montant_reversement'] ?? 0,
            'observation' => $validated['observation'],
            'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
            'date_soumission' => $request->input('action') === 'soumettre' ? now() : $declaration->date_soumission,
        ]);

        // Envoyer notification si soumission
        if ($request->input('action') === 'soumettre') {
            $this->envoyerNotificationSoumissionUnique($declaration);
        }

        Alert::success('Succès', 'Déclaration modifiée avec succès');
        return redirect()->route('pcs.declarations.index');
    }

    /**
     * Envoyer notification de soumission pour une seule déclaration
     */
    private function envoyerNotificationSoumissionUnique($declaration)
    {
        // Récupérer tous les utilisateurs qui peuvent valider les déclarations PCS
        $validateurs = User::where('peut_valider_pcs', true)->get();

        // Envoyer notification à chaque validateur
        foreach ($validateurs as $validateur) {
            $validateur->notify(new PcsDeclarationSoumise($declaration));
        }
    }

    /**
     * Validation d'une déclaration
     */
    public function valider(Request $request, DeclarationPcs $declaration)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous n\'avez pas l\'autorisation de valider');
            return redirect()->back();
        }

        $declaration->valider($user->id);

        // Notifier le déclarant
        if ($declaration->saisiPar) {
            $declaration->saisiPar->notify(new PcsDeclarationValidee($declaration));
        }

        Alert::success('Succès', 'Déclaration validée avec succès');

        return redirect()->back();
    }

    /**
     * Rejet d'une déclaration
     */
    public function rejeter(Request $request, DeclarationPcs $declaration)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'motif_rejet' => 'required|string|min:10',
        ]);

        if (!$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous n\'avez pas l\'autorisation de rejeter');
            return redirect()->back();
        }

        $declaration->rejeter($user->id, $request->motif_rejet);

        // Notifier le déclarant
        if ($declaration->saisiPar) {
            $declaration->saisiPar->notify(new PcsDeclarationRejetee($declaration));
        }

        Alert::success('Succès', 'Déclaration rejetée');

        return redirect()->back();
    }

    /**
     * Génération PDF - État des recettes
     */
    public function generatePdfRecettes(Request $request)
    {
        $programme = $request->input('programme', 'UEMOA');
        $annee = $request->input('annee', date('Y'));

        // Récupérer toutes les déclarations validées pour l'année
        $declarations = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Organiser les données par entité et par mois
        $donnees = $this->organiserDonneesParMois($declarations, 'recouvrement');

        $pdf = Pdf::loadView('pcs.pdf.etat-recettes', compact('donnees', 'programme', 'annee'));
        return $pdf->download("Recettes_{$programme}_{$annee}.pdf");
    }

    /**
     * Génération PDF - État des reversements
     */
    public function generatePdfReversements(Request $request)
    {
        $programme = $request->input('programme', 'UEMOA');
        $annee = $request->input('annee', date('Y'));

        // Récupérer toutes les déclarations validées pour l'année
        $declarations = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Organiser les données par entité et par mois
        $donnees = $this->organiserDonneesParMois($declarations, 'reversement');

        $pdf = Pdf::loadView('pcs.pdf.etat-recettes', compact('donnees', 'programme', 'annee'));
        return $pdf->download("Reversements_{$programme}_{$annee}.pdf");
    }

    /**
     * Organiser les données par mois
     */
    private function organiserDonneesParMois($declarations, $type = 'recouvrement')
    {
        $donnees = [];

        foreach ($declarations as $decl) {
            $nom = $decl->nom_entite;

            if (!isset($donnees[$nom])) {
                $donnees[$nom] = [
                    'nom' => $nom,
                    'type' => $decl->type_entite,
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0,
                ];
            }

            $montant = $type === 'reversement' ? $decl->montant_reversement : $decl->montant_recouvrement;
            $donnees[$nom]['mois'][$decl->mois] = $montant;
            $donnees[$nom]['total'] += $montant;
        }

        return $donnees;
    }

    /**
     * Envoyer notification de soumission aux validateurs
     */
    private function envoyerNotificationSoumission($poste, $request)
    {
        // Récupérer tous les utilisateurs qui peuvent valider les déclarations PCS
        $validateurs = User::where('peut_valider_pcs', true)->get();

        // Récupérer les déclarations créées pour cette soumission
        $declarations = DeclarationPcs::where('saisi_par', Auth::id())
            ->where('date_soumission', now()->format('Y-m-d H:i:s'))
            ->get();

        // Envoyer notification pour chaque déclaration
        foreach ($declarations as $declaration) {
            foreach ($validateurs as $validateur) {
                $validateur->notify(new PcsDeclarationSoumise($declaration));
            }
        }
    }

    /**
     * Générer l'état consolidé des reversements et recouvrements PCS
     */
    public function etatConsolideReversements(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $programme = $request->get('programme', 'UEMOA');

        // Récupérer les données des déclarations PCS
        $declarations = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('annee', $annee)
            ->where('programme', $programme)
            ->get();

        // Organiser les données par poste et mois
        $recouvrementsParPoste = [];
        $reversementsParPoste = [];
        $totalRecouvrementsMensuel = array_fill(1, 12, 0);
        $totalReversementsMensuel = array_fill(1, 12, 0);
        $totalRecouvrements = 0;
        $totalReversements = 0;

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id ? $declaration->poste->nom : $declaration->bureauDouane->libelle;
            $comptable = $declaration->poste_id ? '' : 'ACCT';
            $mois = $declaration->mois;

            // Recouvrements
            if (!isset($recouvrementsParPoste[$nomPoste])) {
                $recouvrementsParPoste[$nomPoste] = [
                    'comptable' => $comptable,
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $recouvrementsParPoste[$nomPoste]['mois'][$mois] += $declaration->montant_recouvrement;
            $recouvrementsParPoste[$nomPoste]['total'] += $declaration->montant_recouvrement;
            $totalRecouvrementsMensuel[$mois] += $declaration->montant_recouvrement;
            $totalRecouvrements += $declaration->montant_recouvrement;

            // Reversements
            if (!isset($reversementsParPoste[$nomPoste])) {
                $reversementsParPoste[$nomPoste] = [
                    'comptable' => $comptable,
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $reversementsParPoste[$nomPoste]['mois'][$mois] += $declaration->montant_reversement;
            $reversementsParPoste[$nomPoste]['total'] += $declaration->montant_reversement;
            $totalReversementsMensuel[$mois] += $declaration->montant_reversement;
            $totalReversements += $declaration->montant_reversement;
        }

        // Trier par ordre alphabétique
        ksort($recouvrementsParPoste);
        ksort($reversementsParPoste);

        $pdf = PDF::loadView('pcs.pdf.etat-reversements-consolide', compact(
            'recouvrementsParPoste',
            'reversementsParPoste',
            'totalRecouvrementsMensuel',
            'totalReversementsMensuel',
            'totalRecouvrements',
            'totalReversements',
            'annee',
            'programme'
        ));

        return $pdf->download("Etat_Reversements_PCS_{$programme}_{$annee}.pdf");
    }

    /**
     * Afficher la vue de filtrage pour les états consolidés
     */
    public function filtreEtat()
    {
        $postes = \App\Models\Poste::orderBy('nom')->get();
        $bureaux = \App\Models\BureauDouane::orderBy('libelle')->get();
        return view('pcs.declarations.filtre-etat', compact('postes', 'bureaux'));
    }

    /**
     * Générer l'état consolidé avec filtres personnalisés
     */
    public function etatConsolideFiltre(Request $request)
    {
        // Validation des paramètres
        $validated = $request->validate([
            'programme' => 'required|in:UEMOA,AES',
            'annee' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'poste_id' => 'nullable|exists:postes,id',
            'bureau_id' => 'nullable|exists:bureaux_douanes,id',
            'mois' => 'nullable|integer|min:1|max:12',
            'format' => 'nullable|in:pdf,excel'
        ]);

        $programme = $validated['programme'];
        $annee = $validated['annee'];
        $dateDebut = $validated['date_debut'];
        $dateFin = $validated['date_fin'];
        $posteId = $validated['poste_id'];
        $bureauId = $validated['bureau_id'];
        $mois = $validated['mois'];
        $format = $validated['format'] ?? 'pdf';

        // Construire la requête avec filtres
        $query = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);

        if ($posteId) {
            $query->where('poste_id', $posteId);
        }

        if ($bureauId) {
            $query->where('bureau_douane_id', $bureauId);
        }

        if ($mois) {
            $query->where('mois', $mois);
        }

        $declarations = $query->get();

        // Organiser les données (même logique que etatConsolideReversements)
        $recouvrementsParPoste = [];
        $reversementsParPoste = [];
        $totalRecouvrementsMensuel = array_fill(1, 12, 0);
        $totalReversementsMensuel = array_fill(1, 12, 0);
        $totalRecouvrements = 0;
        $totalReversements = 0;

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id ? $declaration->poste->nom : $declaration->bureauDouane->libelle;
            $moisDeclaration = $declaration->mois;

            // Recouvrements
            if (!isset($recouvrementsParPoste[$nomPoste])) {
                $recouvrementsParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $recouvrementsParPoste[$nomPoste]['mois'][$moisDeclaration] += $declaration->montant_recouvrement;
            $recouvrementsParPoste[$nomPoste]['total'] += $declaration->montant_recouvrement;
            $totalRecouvrementsMensuel[$moisDeclaration] += $declaration->montant_recouvrement;
            $totalRecouvrements += $declaration->montant_recouvrement;

            // Reversements
            if (!isset($reversementsParPoste[$nomPoste])) {
                $reversementsParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $reversementsParPoste[$nomPoste]['mois'][$moisDeclaration] += $declaration->montant_reversement;
            $reversementsParPoste[$nomPoste]['total'] += $declaration->montant_reversement;
            $totalReversementsMensuel[$moisDeclaration] += $declaration->montant_reversement;
            $totalReversements += $declaration->montant_reversement;
        }

        // Trier par ordre alphabétique
        ksort($recouvrementsParPoste);
        ksort($reversementsParPoste);

        // Générer le nom du fichier avec les filtres
        $nomFichier = "Etat_Reversements_PCS_{$programme}_{$annee}";
        if ($posteId) {
            $poste = \App\Models\Poste::find($posteId);
            $nomFichier .= "_" . strtoupper($poste->nom);
        }
        if ($bureauId) {
            $bureau = \App\Models\BureauDouane::find($bureauId);
            $nomFichier .= "_" . strtoupper($bureau->libelle);
        }
        if ($mois) {
            $nomFichier .= "_" . str_pad($mois, 2, '0', STR_PAD_LEFT);
        }
        $nomFichier .= "_" . str_replace('-', '', $dateDebut) . "_" . str_replace('-', '', $dateFin);

        if ($format === 'pdf') {
            $pdf = PDF::loadView('pcs.pdf.etat-reversements-consolide', compact(
                'recouvrementsParPoste',
                'reversementsParPoste',
                'totalRecouvrementsMensuel',
                'totalReversementsMensuel',
                'totalRecouvrements',
                'totalReversements',
                'annee',
                'programme'
            ));

            return $pdf->download("{$nomFichier}.pdf");
        } else {
            // TODO: Implémenter l'export Excel
            return response()->json(['message' => 'Export Excel en cours de développement']);
        }
    }

    /**
     * API pour les statistiques rapides
     */
    public function statsRapides(Request $request)
    {
        $query = DeclarationPcs::query();

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        }

        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('bureau_id')) {
            $query->where('bureau_douane_id', $request->bureau_id);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        $declarations = $query->get();

        return response()->json([
            'total_declarations' => $declarations->count(),
            'montant_recouvrement' => number_format($declarations->sum('montant_recouvrement'), 0, ',', ' ') . ' FCFA',
            'montant_reversement' => number_format($declarations->sum('montant_reversement'), 0, ',', ' ') . ' FCFA',
            'postes_actifs' => $declarations->pluck('poste_id')->unique()->filter()->count() + $declarations->pluck('bureau_douane_id')->unique()->filter()->count(),
        ]);
    }

    /**
     * Aperçu des données filtrées
     */
    public function apercu(Request $request)
    {
        $query = DeclarationPcs::with(['poste', 'bureauDouane']);

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        }

        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('bureau_id')) {
            $query->where('bureau_douane_id', $request->bureau_id);
        }

        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        $declarations = $query->orderBy('created_at', 'desc')->limit(50)->get();

        if ($request->filled('apercu')) {
            return view('pcs.declarations.apercu', compact('declarations'))->render();
        }

        return view('pcs.declarations.apercu', compact('declarations'));
    }
}


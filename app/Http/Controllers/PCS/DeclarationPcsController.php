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
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Notifications\PcsDeclarationValidee;
use App\Notifications\PcsDeclarationSoumise;

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

        // ACCT et admin voient toutes les déclarations ; les autres voient uniquement leur poste
        $estValideurOuAcct = $user->peut_valider_pcs || $user->hasRole('acct') || $user->hasRole('admin');
        if (!$estValideurOuAcct) {
            $posteId = $user->poste_id;
            $query->where(function ($q) use ($posteId) {
                $q->where('poste_id', $posteId)
                  ->orWhereHas('bureauDouane', function ($q2) use ($posteId) {
                      $q2->where('poste_rgd_id', $posteId);
                  });
            });
        }

        // Récupérer toutes les déclarations
        $toutesDeclarations = $query->get();

        // Grouper par période et entité
        $groupes = $toutesDeclarations->groupBy(function($decl) {
            $entite = $decl->poste_id ? 'P_'.$decl->poste_id : 'B_'.$decl->bureau_douane_id;
            return $decl->annee . '_' . $decl->mois . '_' . $entite;
        });

        // Pagination manuelle des groupes
        $page = $request->get('page', 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $groupesPagines = $groupes->slice($offset, $perPage);

        // Créer un paginator personnalisé
        $declarations = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupesPagines,
            $groupes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Ajouter le total de déclarations pour info
        $totalDeclarations = $toutesDeclarations->count();

        return view('pcs.declarations.index', compact('declarations', 'totalDeclarations'));
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

        // Détecter les mois manquants pour chaque programme
        $moisManquants = [];
        $moisRenseignes = []; // Nouveau : mois déjà renseignés
        $anneeCourante = $annee;

        // Pour chaque programme, détecter les mois non renseignés et les mois déjà renseignés
        foreach ($programmes as $programme) {
            $moisManquants[$programme] = [];
            $moisRenseignes[$programme] = [];

            // Récupérer les déclarations existantes pour ce poste et ce programme
            if ($poste->isRgd()) {
                // Pour RGD, vérifier les déclarations propres (poste_id = poste->id, bureau_douane_id = null)
                $declarationsExistentes = DeclarationPcs::where('poste_id', $poste->id)
                    ->where('bureau_douane_id', null)
                    ->where('programme', $programme)
                    ->where('annee', $anneeCourante)
                    ->pluck('mois')
                    ->toArray();
            } else {
                // Pour poste normal
                $declarationsExistentes = DeclarationPcs::where('poste_id', $poste->id)
                    ->where('programme', $programme)
                    ->where('annee', $anneeCourante)
                    ->pluck('mois')
                    ->toArray();
            }

            // Trouver les mois manquants et les mois déjà renseignés (tous les 12 mois de l'année)
            for ($m = 1; $m <= 12; $m++) {
                if (!in_array($m, $declarationsExistentes)) {
                    $moisManquants[$programme][] = $m;
                } else {
                    $moisRenseignes[$programme][] = $m;
                }
            }
        }

        // Vérifier aussi l'année précédente (derniers mois de l'année passée)
        $anneePrecedente = $anneeCourante - 1;
        $moisManquantsAnneePrecedente = [];
        $moisRenseignesAnneePrecedente = [];

        foreach ($programmes as $programme) {
            $moisManquantsAnneePrecedente[$programme] = [];
            $moisRenseignesAnneePrecedente[$programme] = [];

            if ($poste->isRgd()) {
                $declarationsAnneePrecedente = DeclarationPcs::where('poste_id', $poste->id)
                    ->where('bureau_douane_id', null)
                    ->where('programme', $programme)
                    ->where('annee', $anneePrecedente)
                    ->pluck('mois')
                    ->toArray();
            } else {
                $declarationsAnneePrecedente = DeclarationPcs::where('poste_id', $poste->id)
                    ->where('programme', $programme)
                    ->where('annee', $anneePrecedente)
                    ->pluck('mois')
                    ->toArray();
            }

            // Vérifier les mois de septembre à décembre de l'année précédente
            for ($m = 9; $m <= 12; $m++) {
                if (!in_array($m, $declarationsAnneePrecedente)) {
                    $moisManquantsAnneePrecedente[$programme][] = $m;
                } else {
                    $moisRenseignesAnneePrecedente[$programme][] = $m;
                }
            }
        }

        return view('pcs.declarations.create', compact(
            'poste',
            'bureaux',
            'programmes',
            'mois',
            'annee',
            'moisManquants',
            'moisRenseignes',
            'anneePrecedente',
            'moisManquantsAnneePrecedente',
            'moisRenseignesAnneePrecedente'
        ));
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
            // Vérifier si c'est un mode rattrapage (plusieurs mois)
            $moisSelectionnes = $request->input('mois_selectionnes', []);

            if (!empty($moisSelectionnes) && is_array($moisSelectionnes)) {
                // Mode rattrapage : traiter plusieurs mois
                $this->storeRattrapageMultiple($request, $poste, $user, $moisSelectionnes);
            } else {
                // Mode normal : un seul mois
                if ($poste->isRgd()) {
                    $this->storeDeclarationsRgd($request, $poste, $user);
                } else {
                    $this->storeDeclarationNormale($request, $poste, $user);
                }
            }

            DB::commit();

            // Envoyer notification à l'ACCT si soumission
            if ($request->input('action') === 'soumettre') {
                $this->envoyerNotificationSoumission($poste, $request);
            }

            $nbMois = !empty($moisSelectionnes) ? count($moisSelectionnes) : 1;
            Alert::success('Succès', $nbMois . ' déclaration(s) enregistrée(s) avec succès');
            return redirect()->route('pcs.declarations.index');

        } catch (QueryException $e) {
            DB::rollBack();
            $isDuplicate = $e->getCode() === '23000' || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
            if ($isDuplicate) {
                Alert::error('Doublon', 'Une déclaration existe déjà pour ce poste/bureau, programme et cette période (mois/année). Un seul enregistrement par période est autorisé.');
                return redirect()->back()->withInput();
            }
            Alert::error('Erreur', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Enregistrer le rattrapage de plusieurs mois
     */
    private function storeRattrapageMultiple(Request $request, $poste, $user, $moisSelectionnes)
    {
        $annee = $request->input('annee');

        foreach ($moisSelectionnes as $mois) {
            // Pour chaque mois, traiter les déclarations
            if ($poste->isRgd()) {
                $this->storeDeclarationsRgdRattrapage($request, $poste, $user, $mois, $annee);
            } else {
                $this->storeDeclarationNormaleRattrapage($request, $poste, $user, $mois, $annee);
            }
        }
    }

    /**
     * Enregistrer déclarations RGD en mode rattrapage
     */
    private function storeDeclarationsRgdRattrapage(Request $request, $poste, $user, $mois, $annee)
    {
        // Pour chaque programme (UEMOA et AES)
        foreach (['UEMOA', 'AES'] as $programme) {
            // Déclaration RGD propre
            $recouvrementRgd = $request->input("mois_{$mois}_rgd_{$programme}_recouvrement");
            $reversementRgd = $request->input("mois_{$mois}_rgd_{$programme}_reversement");

            if ($recouvrementRgd || $reversementRgd || $request->input("mois_{$mois}_rgd_{$programme}_reference")) {
                $declaration = DeclarationPcs::updateOrCreate(
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
                        'reference' => $request->input("mois_{$mois}_rgd_{$programme}_reference"),
                        'observation' => $request->input("mois_{$mois}_rgd_{$programme}_observation"),
                        'statut' => $request->input('action') === 'soumettre' ? 'valide' : 'brouillon',
                        'date_saisie' => now(),
                        'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                        'date_validation' => $request->input('action') === 'soumettre' ? now() : null,
                        'valide_par' => $request->input('action') === 'soumettre' ? $user->id : null,
                        'saisi_par' => $user->id,
                    ]
                );
                if ($request->hasFile("mois_{$mois}_rgd_{$programme}_preuve_paiement")) {
                    $path = $request->file("mois_{$mois}_rgd_{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                    $declaration->update(['preuve_paiement' => $path]);
                }
            }

            // Déclarations des bureaux
            $bureaux = $poste->bureauxDouanes()->actif()->get();
            foreach ($bureaux as $bureau) {
                $recouvrementBureau = $request->input("mois_{$mois}_bureau_{$bureau->id}_{$programme}_recouvrement");
                $reversementBureau = $request->input("mois_{$mois}_bureau_{$bureau->id}_{$programme}_reversement");

                if ($recouvrementBureau || $reversementBureau || $request->input("mois_{$mois}_bureau_{$bureau->id}_{$programme}_reference")) {
                    $declaration = DeclarationPcs::updateOrCreate(
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
                            'reference' => $request->input("mois_{$mois}_bureau_{$bureau->id}_{$programme}_reference"),
                            'observation' => $request->input("mois_{$mois}_bureau_{$bureau->id}_{$programme}_observation"),
                            'statut' => $request->input('action') === 'soumettre' ? 'valide' : 'brouillon',
                            'date_saisie' => now(),
                            'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                            'date_validation' => $request->input('action') === 'soumettre' ? now() : null,
                            'valide_par' => $request->input('action') === 'soumettre' ? $user->id : null,
                            'saisi_par' => $user->id,
                        ]
                    );
                    if ($request->hasFile("mois_{$mois}_bureau_{$bureau->id}_{$programme}_preuve_paiement")) {
                        $path = $request->file("mois_{$mois}_bureau_{$bureau->id}_{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                        $declaration->update(['preuve_paiement' => $path]);
                    }
                }
            }
        }
    }

    /**
     * Enregistrer déclaration poste normal en mode rattrapage
     */
    private function storeDeclarationNormaleRattrapage(Request $request, $poste, $user, $mois, $annee)
    {
        foreach (['UEMOA', 'AES'] as $programme) {
            $recouvrement = $request->input("mois_{$mois}_{$programme}_recouvrement");
            $reversement = $request->input("mois_{$mois}_{$programme}_reversement");

            if ($recouvrement || $reversement || $request->input("mois_{$mois}_{$programme}_reference")) {
                $declaration = DeclarationPcs::updateOrCreate(
                    [
                        'poste_id' => $poste->id,
                        'bureau_douane_id' => null,
                        'programme' => $programme,
                        'mois' => $mois,
                        'annee' => $annee,
                    ],
                    [
                        'montant_recouvrement' => $recouvrement ?? 0,
                        'montant_reversement' => $reversement ?? 0,
                        'reference' => $request->input("mois_{$mois}_{$programme}_reference"),
                        'observation' => $request->input("mois_{$mois}_{$programme}_observation"),
                        'statut' => $request->input('action') === 'soumettre' ? 'valide' : 'brouillon',
                        'date_saisie' => now(),
                        'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                        'date_validation' => $request->input('action') === 'soumettre' ? now() : null,
                        'valide_par' => $request->input('action') === 'soumettre' ? $user->id : null,
                        'saisi_par' => $user->id,
                    ]
                );
                if ($request->hasFile("mois_{$mois}_{$programme}_preuve_paiement")) {
                    $path = $request->file("mois_{$mois}_{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                    $declaration->update(['preuve_paiement' => $path]);
                }
            }
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
                $declaration = DeclarationPcs::updateOrCreate(
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
                        'reference' => $request->input("rgd_{$programme}_reference"),
                        'observation' => $request->input("rgd_{$programme}_observation"),
                        'statut' => $request->input('action') === 'soumettre' ? 'valide' : 'brouillon',
                        'date_saisie' => now(),
                        'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                        'date_validation' => $request->input('action') === 'soumettre' ? now() : null,
                        'valide_par' => $request->input('action') === 'soumettre' ? $user->id : null,
                        'saisi_par' => $user->id,
                    ]
                );
                if ($request->hasFile("rgd_{$programme}_preuve_paiement")) {
                    $path = $request->file("rgd_{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                    $declaration->update(['preuve_paiement' => $path]);
                }
            }

            // Déclarations des bureaux
            $bureaux = $poste->bureauxDouanes()->actif()->get();
            foreach ($bureaux as $bureau) {
                $recouvrementBureau = $request->input("bureau_{$bureau->id}_{$programme}_recouvrement");
                $reversementBureau = $request->input("bureau_{$bureau->id}_{$programme}_reversement");

                if ($recouvrementBureau || $reversementBureau) {
                    $declaration = DeclarationPcs::updateOrCreate(
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
                            'reference' => $request->input("bureau_{$bureau->id}_{$programme}_reference"),
                            'observation' => $request->input("bureau_{$bureau->id}_{$programme}_observation"),
                            'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
                            'date_saisie' => now(),
                            'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                            'saisi_par' => $user->id,
                        ]
                    );
                    if ($request->hasFile("bureau_{$bureau->id}_{$programme}_preuve_paiement")) {
                        $path = $request->file("bureau_{$bureau->id}_{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                        $declaration->update(['preuve_paiement' => $path]);
                    }
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
                $declaration = DeclarationPcs::updateOrCreate(
                    [
                        'poste_id' => $poste->id,
                        'bureau_douane_id' => null,
                        'programme' => $programme,
                        'mois' => $request->mois,
                        'annee' => $request->annee,
                    ],
                    [
                        'montant_recouvrement' => $recouvrement ?? 0,
                        'montant_reversement' => $reversement ?? 0,
                        'reference' => $request->input("{$programme}_reference"),
                        'observation' => $request->input("{$programme}_observation"),
                        'statut' => $request->input('action') === 'soumettre' ? 'valide' : 'brouillon',
                        'date_saisie' => now(),
                        'date_soumission' => $request->input('action') === 'soumettre' ? now() : null,
                        'date_validation' => $request->input('action') === 'soumettre' ? now() : null,
                        'valide_par' => $request->input('action') === 'soumettre' ? $user->id : null,
                        'saisi_par' => $user->id,
                    ]
                );
                if ($request->hasFile("{$programme}_preuve_paiement")) {
                    $path = $request->file("{$programme}_preuve_paiement")->store("preuves-pcs/declarations/{$declaration->id}", 'public');
                    $declaration->update(['preuve_paiement' => $path]);
                }
            }
        }
    }

    /**
     * Détail d'une déclaration
     */
    /**
     * Télécharger la preuve de paiement d'une déclaration
     */
    public function preuve(DeclarationPcs $declaration)
    {
        if (!$declaration->preuve_paiement || !Storage::disk('public')->exists($declaration->preuve_paiement)) {
            abort(404, 'Fichier introuvable.');
        }
        $nomOriginal = basename($declaration->preuve_paiement);
        return Storage::disk('public')->download($declaration->preuve_paiement, $nomOriginal);
    }

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

        // Seul le créateur (poste émetteur) peut modifier la déclaration
        if ($declaration->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs déclarations');
            return redirect()->back();
        }

        // Modification permise à tout moment (même si validée ou rejetée)

        return view('pcs.declarations.edit', compact('declaration'));
    }

    /**
     * Mise à jour d'une déclaration
     */
    public function update(Request $request, DeclarationPcs $declaration)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Seul le créateur (poste émetteur) peut modifier la déclaration
        if ($declaration->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs déclarations');
            return redirect()->back();
        }

        // Modification permise à tout moment (même si validée ou rejetée)

        // Validation
        $validated = $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2020',
            'montant_recouvrement' => 'nullable|numeric|min:0',
            'montant_reversement' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'observation' => 'nullable|string',
        ]);

        // Mise à jour
        $declaration->update([
            'mois' => $validated['mois'],
            'annee' => $validated['annee'],
            'montant_recouvrement' => $validated['montant_recouvrement'] ?? 0,
            'montant_reversement' => $validated['montant_reversement'] ?? 0,
            'reference' => $validated['reference'],
            'observation' => $validated['observation'],
            'statut' => $request->input('action') === 'soumettre' ? 'valide' : $declaration->statut,
            'date_soumission' => $request->input('action') === 'soumettre' ? now() : $declaration->date_soumission,
            'date_validation' => $request->input('action') === 'soumettre' ? now() : $declaration->date_validation,
            'valide_par' => $request->input('action') === 'soumettre' ? $user->id : $declaration->valide_par,
        ]);

        // Envoyer notification à l'ACCT si soumission
        if ($request->input('action') === 'soumettre') {
            $this->envoyerNotificationSoumissionUnique($declaration);
        }

        Alert::success('Succès', 'Déclaration modifiée avec succès');
        return redirect()->route('pcs.declarations.index');
    }

    /**
     * Envoyer notification de soumission à l'ACCT pour une seule déclaration
     */
    private function envoyerNotificationSoumissionUnique($declaration)
    {
        // Charger les relations nécessaires pour la notification
        $declaration->load(['poste', 'bureauDouane']);

        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Envoyer notification à chaque utilisateur ACCT
        foreach ($acctUsers as $acctUser) {
            $acctUser->notify(new PcsDeclarationSoumise($declaration));
        }
    }

    /**
     * Envoyer notification de validation à l'ACCT pour une seule déclaration
     */
    private function envoyerNotificationValidationUnique($declaration)
    {
        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Envoyer notification à chaque utilisateur ACCT
        foreach ($acctUsers as $acctUser) {
            $acctUser->notify(new PcsDeclarationValidee($declaration));
        }
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
     * Envoyer notification de soumission à l'ACCT pour les déclarations créées
     */
    private function envoyerNotificationSoumission($poste, $request)
    {
        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Récupérer les déclarations créées pour cette soumission
        // On utilise une plage de temps pour récupérer les déclarations créées pendant la transaction
        $declarations = DeclarationPcs::where('saisi_par', Auth::id())
            ->where('date_soumission', '>=', now()->subMinutes(1)->format('Y-m-d H:i:s'))
            ->where('statut', 'valide')
            ->get();

        // Envoyer notification pour chaque déclaration à chaque utilisateur ACCT
        foreach ($declarations as $declaration) {
            // Charger les relations nécessaires pour la notification
            $declaration->load(['poste', 'bureauDouane']);

            foreach ($acctUsers as $acctUser) {
                $acctUser->notify(new PcsDeclarationSoumise($declaration));
            }
        }
    }

    /**
     * Envoyer notification de validation à l'ACCT pour les déclarations créées
     */
    private function envoyerNotificationValidation($poste, $request)
    {
        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Récupérer les déclarations créées pour cette soumission (validées)
        // On utilise une plage de temps pour récupérer les déclarations créées pendant la transaction
        $declarations = DeclarationPcs::where('saisi_par', Auth::id())
            ->where('date_soumission', '>=', now()->subMinutes(1)->format('Y-m-d H:i:s'))
            ->where('statut', 'valide')
            ->get();

        // Envoyer notification pour chaque déclaration à chaque utilisateur ACCT
        foreach ($declarations as $declaration) {
            foreach ($acctUsers as $acctUser) {
                $acctUser->notify(new PcsDeclarationValidee($declaration));
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

    /**
     * Générer l'état consolidé pour un poste émetteur
     */
    public function etatConsolidePosteEmetteur(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->poste_id) {
            Alert::error('Erreur', 'Aucun poste assigné à votre compte');
            return redirect()->route('pcs.declarations.index');
        }

        $annee = $request->get('annee', date('Y'));
        $programme = $request->get('programme', 'UEMOA');
        $poste = $user->poste;

        // Récupérer les déclarations du poste émetteur
        $query = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('annee', $annee)
            ->where('programme', $programme);

        // Pour RGD, récupérer aussi les déclarations des bureaux
        if ($poste->isRgd()) {
            $bureauxIds = \App\Models\BureauDouane::where('poste_rgd_id', $poste->id)
                ->where('actif', true)
                ->pluck('id');
            $query->where(function($q) use ($poste, $bureauxIds) {
                $q->where('poste_id', $poste->id)
                  ->whereNull('bureau_douane_id')
                  ->orWhereIn('bureau_douane_id', $bureauxIds);
            });
        } else {
            $query->where('poste_id', $poste->id)
                  ->whereNull('bureau_douane_id');
        }

        $declarations = $query->get();

        // Organiser les données par entité (poste ou bureau) et par mois
        $recouvrementsParEntite = [];
        $reversementsParEntite = [];
        $totalRecouvrementsMensuel = array_fill(1, 12, 0);
        $totalReversementsMensuel = array_fill(1, 12, 0);
        $totalRecouvrements = 0;
        $totalReversements = 0;

        foreach ($declarations as $declaration) {
            $nomEntite = $declaration->poste_id && !$declaration->bureau_douane_id
                ? $declaration->poste->nom
                : $declaration->bureauDouane->libelle;
            $mois = $declaration->mois;

            // Recouvrements
            if (!isset($recouvrementsParEntite[$nomEntite])) {
                $recouvrementsParEntite[$nomEntite] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $recouvrementsParEntite[$nomEntite]['mois'][$mois] += $declaration->montant_recouvrement;
            $recouvrementsParEntite[$nomEntite]['total'] += $declaration->montant_recouvrement;
            $totalRecouvrementsMensuel[$mois] += $declaration->montant_recouvrement;
            $totalRecouvrements += $declaration->montant_recouvrement;

            // Reversements
            if (!isset($reversementsParEntite[$nomEntite])) {
                $reversementsParEntite[$nomEntite] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $reversementsParEntite[$nomEntite]['mois'][$mois] += $declaration->montant_reversement;
            $reversementsParEntite[$nomEntite]['total'] += $declaration->montant_reversement;
            $totalReversementsMensuel[$mois] += $declaration->montant_reversement;
            $totalReversements += $declaration->montant_reversement;
        }

        // Trier par ordre alphabétique
        ksort($recouvrementsParEntite);
        ksort($reversementsParEntite);

        $pdf = PDF::loadView('pcs.pdf.etat-reversements-consolide-poste-emetteur', compact(
            'recouvrementsParEntite',
            'reversementsParEntite',
            'totalRecouvrementsMensuel',
            'totalReversementsMensuel',
            'totalRecouvrements',
            'totalReversements',
            'annee',
            'programme',
            'poste'
        ));

        return $pdf->download("Etat_Reversements_PCS_{$programme}_{$poste->nom}_{$annee}.pdf");
    }
}


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
use Barryvdh\DomPDF\Facade\Pdf;
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
}


<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\DestockagePcs;
use App\Models\DestockagePcsPoste;
use App\Models\DeclarationPcs;
use App\Models\Poste;
use App\Models\BureauDouane;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class DestockagePcsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vérifier que l'utilisateur est ACCT ou Admin
     */
    private function authorizeAcct()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['acct', 'admin'])) {
            abort(403, 'Accès refusé. Cette fonctionnalité est réservée à l\'ACCT.');
        }
    }

    /**
     * Vue de collecte - Afficher les fonds collectés par poste
     */
    public function collecte(Request $request)
    {
        $this->authorizeAcct();

        $programme = $request->get('programme', 'UEMOA');
        $mois = $request->get('mois', date('n'));
        $annee = $request->get('annee', date('Y'));

        // Récupérer toutes les déclarations validées pour cette période et ce programme
        $declarations = DeclarationPcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->with(['poste', 'bureauDouane'])
            ->get();

        // Organiser les données par poste/bureau
        $collectesParPoste = [];
        
        foreach ($declarations as $declaration) {
            $entiteId = $declaration->poste_id ?? 'bureau_' . $declaration->bureau_douane_id;
            $nomEntite = $declaration->poste_id 
                ? ($declaration->poste->nom ?? 'N/A')
                : ($declaration->bureauDouane->libelle ?? 'N/A');
            $typeEntite = $declaration->poste_id ? 'poste' : 'bureau';

            if (!isset($collectesParPoste[$entiteId])) {
                $collectesParPoste[$entiteId] = [
                    'id' => $entiteId,
                    'nom' => $nomEntite,
                    'type' => $typeEntite,
                    'poste_id' => $declaration->poste_id,
                    'bureau_douane_id' => $declaration->bureau_douane_id,
                    'montant_collecte' => 0,
                    'montant_deja_destocke' => 0,
                    'solde_disponible' => 0,
                ];
            }

            $collectesParPoste[$entiteId]['montant_collecte'] += $declaration->montant_recouvrement;
        }

        // Calculer les montants déjà déstockés pour chaque poste
        foreach ($collectesParPoste as $entiteId => &$collecte) {
            $dejaDestocke = 0;
            
            if ($collecte['type'] === 'poste') {
                $dejaDestocke = DestockagePcsPoste::where('poste_id', $collecte['poste_id'])
                    ->whereHas('destockage', function ($q) use ($programme, $mois, $annee) {
                        $q->where('programme', $programme)
                          ->where('periode_mois', $mois)
                          ->where('periode_annee', $annee)
                          ->where('statut', 'valide');
                    })
                    ->sum('montant_destocke');
            } else {
                $dejaDestocke = DestockagePcsPoste::where('bureau_douane_id', $collecte['bureau_douane_id'])
                    ->whereHas('destockage', function ($q) use ($programme, $mois, $annee) {
                        $q->where('programme', $programme)
                          ->where('periode_mois', $mois)
                          ->where('periode_annee', $annee)
                          ->where('statut', 'valide');
                    })
                    ->sum('montant_destocke');
            }

            $collecte['montant_deja_destocke'] = $dejaDestocke;
            $collecte['solde_disponible'] = $collecte['montant_collecte'] - $dejaDestocke;
        }

        // Trier par nom
        usort($collectesParPoste, function ($a, $b) {
            return strcmp($a['nom'], $b['nom']);
        });

        // Générer les années et mois pour les filtres
        $annees = range(date('Y') - 2, date('Y') + 1);
        $moisList = [];
        for ($i = 1; $i <= 12; $i++) {
            $moisList[$i] = Carbon::create()->month($i)->locale('fr')->translatedFormat('F');
        }

        return view('pcs.destockages.collecte', compact(
            'collectesParPoste',
            'programme',
            'mois',
            'annee',
            'annees',
            'moisList'
        ));
    }

    /**
     * Formulaire de création de déstockage
     */
    public function create(Request $request)
    {
        $this->authorizeAcct();

        $programme = $request->get('programme', 'UEMOA');
        $mois = $request->get('mois', date('n'));
        $annee = $request->get('annee', date('Y'));

        // Récupérer les données de collecte (même logique que collecte)
        $declarations = DeclarationPcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->with(['poste', 'bureauDouane'])
            ->get();

        $collectesParPoste = [];
        
        foreach ($declarations as $declaration) {
            $entiteId = $declaration->poste_id ?? 'bureau_' . $declaration->bureau_douane_id;
            $nomEntite = $declaration->poste_id 
                ? ($declaration->poste->nom ?? 'N/A')
                : ($declaration->bureauDouane->libelle ?? 'N/A');
            $typeEntite = $declaration->poste_id ? 'poste' : 'bureau';

            if (!isset($collectesParPoste[$entiteId])) {
                $collectesParPoste[$entiteId] = [
                    'id' => $entiteId,
                    'nom' => $nomEntite,
                    'type' => $typeEntite,
                    'poste_id' => $declaration->poste_id,
                    'bureau_douane_id' => $declaration->bureau_douane_id,
                    'montant_collecte' => 0,
                    'montant_deja_destocke' => 0,
                    'solde_disponible' => 0,
                ];
            }

            $collectesParPoste[$entiteId]['montant_collecte'] += $declaration->montant_recouvrement;
        }

        foreach ($collectesParPoste as $entiteId => &$collecte) {
            $dejaDestocke = 0;
            
            if ($collecte['type'] === 'poste') {
                $dejaDestocke = DestockagePcsPoste::where('poste_id', $collecte['poste_id'])
                    ->whereHas('destockage', function ($q) use ($programme, $mois, $annee) {
                        $q->where('programme', $programme)
                          ->where('periode_mois', $mois)
                          ->where('periode_annee', $annee)
                          ->where('statut', 'valide');
                    })
                    ->sum('montant_destocke');
            } else {
                $dejaDestocke = DestockagePcsPoste::where('bureau_douane_id', $collecte['bureau_douane_id'])
                    ->whereHas('destockage', function ($q) use ($programme, $mois, $annee) {
                        $q->where('programme', $programme)
                          ->where('periode_mois', $mois)
                          ->where('periode_annee', $annee)
                          ->where('statut', 'valide');
                    })
                    ->sum('montant_destocke');
            }

            $collecte['montant_deja_destocke'] = $dejaDestocke;
            $collecte['solde_disponible'] = $collecte['montant_collecte'] - $dejaDestocke;
        }

        usort($collectesParPoste, function ($a, $b) {
            return strcmp($a['nom'], $b['nom']);
        });

        // Générer les années et mois pour les filtres
        $annees = range(date('Y') - 2, date('Y') + 1);
        $moisList = [];
        for ($i = 1; $i <= 12; $i++) {
            $moisList[$i] = Carbon::create()->month($i)->locale('fr')->translatedFormat('F');
        }

        return view('pcs.destockages.create', compact(
            'collectesParPoste',
            'programme',
            'mois',
            'annee',
            'annees',
            'moisList'
        ));
    }

    /**
     * Enregistrer un nouveau déstockage
     */
    public function store(Request $request)
    {
        $this->authorizeAcct();

        $validated = $request->validate([
            'programme' => 'required|in:UEMOA,AES',
            'periode_mois' => 'required|integer|between:1,12',
            'periode_annee' => 'required|integer|min:2020',
            'date_destockage' => 'required|date',
            'observation' => 'nullable|string',
            'postes' => 'required|array|min:1',
            'postes.*.id' => 'required|string',
            'postes.*.montant_destocke' => 'required|numeric|min:0.01',
        ]);

        // Filtrer les postes avec montant > 0 (seuls les postes sélectionnés sont envoyés)
        $postesSelectionnes = array_filter($validated['postes'], function($poste) {
            return isset($poste['montant_destocke']) && $poste['montant_destocke'] > 0;
        });

        if (empty($postesSelectionnes)) {
            Alert::error('Erreur', 'Veuillez sélectionner au moins un poste avec un montant à déstocker.');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            // Générer la référence
            $reference = DestockagePcs::genererReference(
                $validated['programme'],
                $validated['periode_mois'],
                $validated['periode_annee']
            );

            // Calculer le montant total
            $montantTotal = 0;
            foreach ($postesSelectionnes as $poste) {
                $montantTotal += $poste['montant_destocke'];
            }

            // Créer le déstockage
            $destockage = DestockagePcs::create([
                'reference_destockage' => $reference,
                'programme' => $validated['programme'],
                'periode_mois' => $validated['periode_mois'],
                'periode_annee' => $validated['periode_annee'],
                'montant_total_destocke' => $montantTotal,
                'date_destockage' => $validated['date_destockage'],
                'observation' => $validated['observation'] ?? null,
                'statut' => 'valide', // Automatiquement validé
                'cree_par' => Auth::id(),
            ]);

            // Créer les lignes de déstockage par poste
            foreach ($postesSelectionnes as $posteData) {
                $entiteId = $posteData['id'];
                $montantDestocke = $posteData['montant_destocke'];
                
                // Déterminer si c'est un poste ou un bureau
                if (str_starts_with($entiteId, 'bureau_')) {
                    $bureauId = str_replace('bureau_', '', $entiteId);
                    $posteId = null;
                } else {
                    $posteId = $entiteId;
                    $bureauId = null;
                }

                // Récupérer le montant collecté et déjà déstocké
                $montantCollecte = 0;
                if ($posteId) {
                    $montantCollecte = DeclarationPcs::where('statut', 'valide')
                        ->where('programme', $validated['programme'])
                        ->where('mois', $validated['periode_mois'])
                        ->where('annee', $validated['periode_annee'])
                        ->where('poste_id', $posteId)
                        ->sum('montant_recouvrement');
                } else {
                    $montantCollecte = DeclarationPcs::where('statut', 'valide')
                        ->where('programme', $validated['programme'])
                        ->where('mois', $validated['periode_mois'])
                        ->where('annee', $validated['periode_annee'])
                        ->where('bureau_douane_id', $bureauId)
                        ->sum('montant_recouvrement');
                }

                // Calculer le montant déjà déstocké avant ce déstockage
                $dejaDestocke = 0;
                if ($posteId) {
                    $dejaDestocke = DestockagePcsPoste::where('poste_id', $posteId)
                        ->whereHas('destockage', function ($q) use ($validated) {
                            $q->where('programme', $validated['programme'])
                              ->where('periode_mois', $validated['periode_mois'])
                              ->where('periode_annee', $validated['periode_annee'])
                              ->where('statut', 'valide');
                        })
                        ->sum('montant_destocke');
                } else {
                    $dejaDestocke = DestockagePcsPoste::where('bureau_douane_id', $bureauId)
                        ->whereHas('destockage', function ($q) use ($validated) {
                            $q->where('programme', $validated['programme'])
                              ->where('periode_mois', $validated['periode_mois'])
                              ->where('periode_annee', $validated['periode_annee'])
                              ->where('statut', 'valide');
                        })
                        ->sum('montant_destocke');
                }

                $soldeAvant = $montantCollecte - $dejaDestocke;
                $soldeApres = $soldeAvant - $montantDestocke;

                // Vérifier que le montant à déstocker ne dépasse pas le disponible
                if ($montantDestocke > $soldeAvant) {
                    throw new \Exception("Le montant à déstocker ({$montantDestocke}) dépasse le solde disponible ({$soldeAvant}) pour cette entité.");
                }

                DestockagePcsPoste::create([
                    'destockage_pcs_id' => $destockage->id,
                    'poste_id' => $posteId,
                    'bureau_douane_id' => $bureauId,
                    'montant_collecte' => $montantCollecte,
                    'montant_destocke' => $montantDestocke,
                    'solde_avant' => $soldeAvant,
                    'solde_apres' => $soldeApres,
                ]);
            }

            DB::commit();

            Alert::success('Succès', 'Déstockage créé avec succès');
            return redirect()->route('pcs.destockages.show', $destockage);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', 'Erreur lors de la création du déstockage: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Liste des déstockages
     */
    public function index(Request $request)
    {
        $this->authorizeAcct();

        $query = DestockagePcs::with(['creePar', 'postes.poste', 'postes.bureauDouane'])
            ->orderBy('periode_annee', 'desc')
            ->orderBy('periode_mois', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        if ($request->filled('mois')) {
            $query->where('periode_mois', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->where('periode_annee', $request->annee);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $destockages = $query->paginate(20);

        // Générer les années et mois pour les filtres
        $annees = range(date('Y') - 2, date('Y') + 1);
        $moisList = [];
        for ($i = 1; $i <= 12; $i++) {
            $moisList[$i] = Carbon::create()->month($i)->locale('fr')->translatedFormat('F');
        }

        return view('pcs.destockages.index', compact('destockages', 'annees', 'moisList'));
    }

    /**
     * Détail d'un déstockage
     */
    public function show(DestockagePcs $destockage)
    {
        $this->authorizeAcct();

        $destockage->load(['creePar', 'postes.poste', 'postes.bureauDouane']);

        return view('pcs.destockages.show', compact('destockage'));
    }

    /**
     * Générer le PDF du bordereau de déstockage
     */
    public function pdf(DestockagePcs $destockage)
    {
        $this->authorizeAcct();

        $destockage->load(['creePar', 'postes.poste', 'postes.bureauDouane']);

        $pdf = PDF::loadView('pcs.pdf.bordereau-destockage', compact('destockage'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Bordereau_Destockage_{$destockage->reference_destockage}.pdf");
    }

    /**
     * État de collecte PCS - Vue PDF
     */
    public function etatCollectePdf(Request $request)
    {
        $this->authorizeAcct();

        $programme = $request->get('programme', 'UEMOA');
        $annee = $request->get('annee', date('Y'));

        // Récupérer toutes les déclarations validées pour cette année et ce programme
        $declarations = DeclarationPcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->with(['poste', 'bureauDouane'])
            ->get();

        // Organiser par poste et par mois
        $collectesParPoste = [];
        $totalCollecteMensuel = array_fill(1, 12, 0);
        $totalDestockeMensuel = array_fill(1, 12, 0);
        $totalCollecteGeneral = 0;

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id 
                ? ($declaration->poste->nom ?? 'N/A')
                : ($declaration->bureauDouane->libelle ?? 'N/A');
            
            if (!isset($collectesParPoste[$nomPoste])) {
                $collectesParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0,
                ];
            }

            $montant = $declaration->montant_recouvrement;
            $collectesParPoste[$nomPoste]['mois'][$declaration->mois] += $montant;
            $collectesParPoste[$nomPoste]['total'] += $montant;
            $totalCollecteMensuel[$declaration->mois] += $montant;
            $totalCollecteGeneral += $montant;
        }

        // Calculer les déstockages
        $destockages = DestockagePcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('periode_annee', $annee)
            ->with(['postes.poste', 'postes.bureauDouane'])
            ->get();

        $totalDestockeGeneral = 0;
        foreach ($destockages as $destockage) {
            foreach ($destockage->postes as $posteDestockage) {
                $totalDestockeMensuel[$destockage->periode_mois] += $posteDestockage->montant_destocke;
                $totalDestockeGeneral += $posteDestockage->montant_destocke;
            }
        }

        // Trier les postes par ordre alphabétique
        ksort($collectesParPoste);

        $pdf = PDF::loadView('pcs.pdf.etat-collecte-pcs', compact(
            'collectesParPoste',
            'totalCollecteMensuel',
            'totalDestockeMensuel',
            'totalCollecteGeneral',
            'totalDestockeGeneral',
            'programme',
            'annee'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("Etat_Collecte_PCS_{$programme}_{$annee}.pdf");
    }

    /**
     * Vue des états interactifs
     */
    public function etats()
    {
        $this->authorizeAcct();
        
        return view('pcs.destockages.etats');
    }

    /**
     * État consolidé des déstockages - Vue PDF
     */
    public function etatConsolidePdf(Request $request)
    {
        $this->authorizeAcct();

        $programme = $request->get('programme', 'UEMOA');
        $annee = $request->get('annee', date('Y'));

        // Récupérer tous les déstockages validés pour cette année et ce programme
        $destockages = DestockagePcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('periode_annee', $annee)
            ->with(['postes.poste', 'postes.bureauDouane'])
            ->get();

        // Organiser par poste et par mois
        $destockagesParPoste = [];
        $totalDestockeMensuel = array_fill(1, 12, 0);
        $totalDestockeGeneral = 0;

        foreach ($destockages as $destockage) {
            foreach ($destockage->postes as $posteDestockage) {
                $nomPoste = $posteDestockage->poste_id 
                    ? ($posteDestockage->poste->nom ?? 'N/A')
                    : ($posteDestockage->bureauDouane->libelle ?? 'N/A');
                
                if (!isset($destockagesParPoste[$nomPoste])) {
                    $destockagesParPoste[$nomPoste] = [
                        'mois' => array_fill(1, 12, 0),
                        'total' => 0,
                    ];
                }

                $montant = $posteDestockage->montant_destocke;
                $destockagesParPoste[$nomPoste]['mois'][$destockage->periode_mois] += $montant;
                $destockagesParPoste[$nomPoste]['total'] += $montant;
                $totalDestockeMensuel[$destockage->periode_mois] += $montant;
                $totalDestockeGeneral += $montant;
            }
        }

        // Calculer le total collecté pour le ratio
        $totalCollecteGeneral = DeclarationPcs::where('statut', 'valide')
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->sum('montant_recouvrement');

        // Trier les postes par ordre alphabétique
        ksort($destockagesParPoste);

        $pdf = PDF::loadView('pcs.pdf.etat-destockages-consolide', compact(
            'destockagesParPoste',
            'totalDestockeMensuel',
            'totalDestockeGeneral',
            'totalCollecteGeneral',
            'programme',
            'annee'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("Etat_Destockages_Consolide_{$programme}_{$annee}.pdf");
    }
}

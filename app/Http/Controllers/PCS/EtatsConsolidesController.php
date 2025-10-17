<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\DeclarationPcs;
use App\Models\AutreDemande;
use App\Models\Poste;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class EtatsConsolidesController extends Controller
{
    /**
     * Afficher la page d'interface unifiée
     */
    public function index()
    {
        $postes = Poste::orderBy('nom')->get();
        return view('pcs.etats-consolides.index', compact('postes'));
    }

    /**
     * Générer l'état selon le type sélectionné
     */
    public function generer(Request $request)
    {
        $type = $request->get('type');
        $annee = $request->get('annee', date('Y'));
        $programme = $request->get('programme', 'UEMOA');

        switch ($type) {
            case 'recouvrements':
                return $this->genererRecouvrements($request);
            case 'reversements':
                return $this->genererReversements($request);
            case 'autres-demandes':
                return $this->genererAutresDemandes($request);
            default:
                return response()->json(['error' => 'Type d\'état non reconnu'], 400);
        }
    }

    /**
     * Générer l'état des recouvrements
     */
    private function genererRecouvrements(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $programme = $request->get('programme', 'UEMOA');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $posteId = $request->get('poste_id');
        $mois = $request->get('mois');

        $query = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('annee', $annee)
            ->where('programme', $programme)
            ->where('statut', 'valide'); // Uniquement les déclarations validées

        if ($dateDebut && $dateFin) {
            $query->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);
        }
        if ($posteId) $query->where('poste_id', $posteId);
        if ($mois) $query->where('mois', $mois);

        $declarations = $query->get();

        // Organiser les données
        $recouvrementsParPoste = [];
        $totalRecouvrementsMensuel = array_fill(1, 12, 0);
        $totalRecouvrements = 0;

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id ? $declaration->poste->nom : $declaration->bureauDouane->libelle;
            $moisDeclaration = $declaration->mois;

            // Convertir en millions de FCFA
            $montantRecouvrement = $declaration->montant_recouvrement / 1000000;

            if (!isset($recouvrementsParPoste[$nomPoste])) {
                $recouvrementsParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $recouvrementsParPoste[$nomPoste]['mois'][$moisDeclaration] += $montantRecouvrement;
            $recouvrementsParPoste[$nomPoste]['total'] += $montantRecouvrement;
            $totalRecouvrementsMensuel[$moisDeclaration] += $montantRecouvrement;
            $totalRecouvrements += $montantRecouvrement;
        }

        ksort($recouvrementsParPoste);

        $reversementsParPoste = []; // Vide pour cet état
        $totalReversementsMensuel = array_fill(1, 12, 0);
        $totalReversements = 0;

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

        return $pdf->download("Etat_Recouvrements_PCS_{$programme}_{$annee}.pdf");
    }

    /**
     * Générer l'état des reversements
     */
    private function genererReversements(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $programme = $request->get('programme', 'UEMOA');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $posteId = $request->get('poste_id');
        $mois = $request->get('mois');

        $query = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('annee', $annee)
            ->where('programme', $programme)
            ->where('statut', 'valide'); // Uniquement les déclarations validées

        if ($dateDebut && $dateFin) {
            $query->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);
        }
        if ($posteId) $query->where('poste_id', $posteId);
        if ($mois) $query->where('mois', $mois);

        $declarations = $query->get();

        // Organiser les données
        $recouvrementsParPoste = [];
        $reversementsParPoste = [];
        $totalRecouvrementsMensuel = array_fill(1, 12, 0);
        $totalReversementsMensuel = array_fill(1, 12, 0);
        $totalRecouvrements = 0;
        $totalReversements = 0;

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id ? $declaration->poste->nom : $declaration->bureauDouane->libelle;
            $moisDeclaration = $declaration->mois;

            // Convertir en millions de FCFA
            $montantRecouvrement = $declaration->montant_recouvrement / 1000000;
            $montantReversement = $declaration->montant_reversement / 1000000;

            // Recouvrements
            if (!isset($recouvrementsParPoste[$nomPoste])) {
                $recouvrementsParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $recouvrementsParPoste[$nomPoste]['mois'][$moisDeclaration] += $montantRecouvrement;
            $recouvrementsParPoste[$nomPoste]['total'] += $montantRecouvrement;
            $totalRecouvrementsMensuel[$moisDeclaration] += $montantRecouvrement;
            $totalRecouvrements += $montantRecouvrement;

            // Reversements
            if (!isset($reversementsParPoste[$nomPoste])) {
                $reversementsParPoste[$nomPoste] = [
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }
            $reversementsParPoste[$nomPoste]['mois'][$moisDeclaration] += $montantReversement;
            $reversementsParPoste[$nomPoste]['total'] += $montantReversement;
            $totalReversementsMensuel[$moisDeclaration] += $montantReversement;
            $totalReversements += $montantReversement;
        }

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
     * Générer l'état des autres demandes
     */
    private function genererAutresDemandes(Request $request)
    {
        $annee = $request->get('annee', date('Y'));
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $posteId = $request->get('poste_id');

        // Ne récupérer que les demandes validées
        $query = AutreDemande::with('poste')
            ->where('annee', $annee)
            ->where('statut', 'valide');

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date_demande', [$dateDebut, $dateFin]);
        }
        if ($posteId) $query->where('poste_id', $posteId);

        $autresDemandes = $query->get();

        // Organiser les données
        $demandesSoumisesParPoste = [];
        $demandesValideesParPoste = [];
        $recapitulatifParPoste = [];

        $totalDemandesSoumisesMensuel = array_fill(1, 12, 0);
        $totalDemandesValideesMensuel = array_fill(1, 12, 0);
        $totalDemandesSoumises = 0;
        $totalDemandesValidees = 0;
        $totalGeneral = [
            'montant_demande' => 0,
            'montant_accord' => 0,
            'pourcentage_accord' => 0
        ];

        foreach ($autresDemandes as $demande) {
            $nomPoste = $demande->poste->nom;
            $mois = \Carbon\Carbon::parse($demande->date_demande)->month;

            // Initialiser les structures si nécessaire
            if (!isset($demandesSoumisesParPoste[$nomPoste])) {
                $demandesSoumisesParPoste[$nomPoste] = [
                    'comptable' => '',
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            if (!isset($demandesValideesParPoste[$nomPoste])) {
                $demandesValideesParPoste[$nomPoste] = [
                    'comptable' => '',
                    'mois' => array_fill(1, 12, 0),
                    'total' => 0
                ];
            }

            if (!isset($recapitulatifParPoste[$nomPoste])) {
                $recapitulatifParPoste[$nomPoste] = [
                    'comptable' => '',
                    'montant_demande' => 0,
                    'montant_accord' => 0,
                    'dernier_mois' => '',
                    'statut' => 'BROUILLON'
                ];
            }

            // Demandes soumises
            if ($demande->statut === 'soumis') {
                $demandesSoumisesParPoste[$nomPoste]['mois'][$mois] += $demande->montant;
                $demandesSoumisesParPoste[$nomPoste]['total'] += $demande->montant;
                $totalDemandesSoumisesMensuel[$mois] += $demande->montant;
                $totalDemandesSoumises += $demande->montant;
            }

            // Demandes validées
            if ($demande->statut === 'valide') {
                $montantAccorde = $demande->montant_accord ?? $demande->montant;
                $demandesValideesParPoste[$nomPoste]['mois'][$mois] += $montantAccorde;
                $demandesValideesParPoste[$nomPoste]['total'] += $montantAccorde;
                $totalDemandesValideesMensuel[$mois] += $montantAccorde;
                $totalDemandesValidees += $montantAccorde;
            }

            // Récapitulatif
            $recapitulatifParPoste[$nomPoste]['montant_demande'] += $demande->montant;
            $recapitulatifParPoste[$nomPoste]['montant_accord'] += $demande->montant_accord ?? 0;
            $recapitulatifParPoste[$nomPoste]['dernier_mois'] = \Carbon\Carbon::parse($demande->date_demande)->format('m/Y');

            if ($demande->statut === 'valide') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'VALIDÉ';
            } elseif ($demande->statut === 'soumis') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'SOUMIS';
            } elseif ($demande->statut === 'rejete') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'REJETÉ';
            }
        }

        // Calculer les pourcentages et les totaux généraux
        $totalGeneral['montant_demande'] = 0;
        $totalGeneral['montant_accord'] = 0;

        foreach ($recapitulatifParPoste as $nomPoste => $data) {
            $pourcentage = $data['montant_demande'] > 0
                ? round(($data['montant_accord'] / $data['montant_demande']) * 100, 1)
                : 0;
            $recapitulatifParPoste[$nomPoste]['pourcentage_accord'] = $pourcentage;

            // Additionner pour le total général
            $totalGeneral['montant_demande'] += $data['montant_demande'];
            $totalGeneral['montant_accord'] += $data['montant_accord'];
        }

        // Calculer le pourcentage général
        $totalGeneral['pourcentage_accord'] = $totalGeneral['montant_demande'] > 0
            ? round(($totalGeneral['montant_accord'] / $totalGeneral['montant_demande']) * 100, 1)
            : 0;

        ksort($demandesSoumisesParPoste);
        ksort($demandesValideesParPoste);
        ksort($recapitulatifParPoste);

        $pdf = PDF::loadView('pcs.pdf.etat-autres-demandes-consolide', compact(
            'autresDemandes',
            'demandesSoumisesParPoste',
            'demandesValideesParPoste',
            'recapitulatifParPoste',
            'totalDemandesSoumisesMensuel',
            'totalDemandesValideesMensuel',
            'totalDemandesSoumises',
            'totalDemandesValidees',
            'totalGeneral',
            'annee'
        ));

        return $pdf->download("Etat_Autres_Demandes_PCS_{$annee}.pdf");
    }

    /**
     * Aperçu des données
     */
    public function apercu(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'recouvrements':
            case 'reversements':
                return $this->apercuDeclarations($request);
            case 'autres-demandes':
                return $this->apercuAutresDemandes($request);
            default:
                return '<div class="alert alert-warning">Type d\'état non reconnu</div>';
        }
    }

    private function apercuDeclarations(Request $request)
    {
        $query = DeclarationPcs::with(['poste', 'bureauDouane']);

        if ($request->filled('programme')) $query->where('programme', $request->programme);
        if ($request->filled('annee')) $query->where('annee', $request->annee);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        }
        if ($request->filled('poste_id')) $query->where('poste_id', $request->poste_id);
        if ($request->filled('mois')) $query->where('mois', $request->mois);

        $declarations = $query->orderBy('created_at', 'desc')->limit(50)->get();
        return view('pcs.declarations.apercu', compact('declarations'))->render();
    }

    private function apercuAutresDemandes(Request $request)
    {
        $query = AutreDemande::with('poste');

        if ($request->filled('annee')) $query->where('annee', $request->annee);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_demande', [$request->date_debut, $request->date_fin]);
        }
        if ($request->filled('poste_id')) $query->where('poste_id', $request->poste_id);
        if ($request->filled('statut')) $query->where('statut', $request->statut);

        $demandes = $query->orderBy('date_demande', 'desc')->limit(50)->get();
        return view('pcs.autres-demandes.apercu', compact('demandes'))->render();
    }

    /**
     * Statistiques
     */
    public function stats(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'recouvrements':
                return $this->statsRecouvrements($request);
            case 'reversements':
                return $this->statsReversements($request);
            case 'autres-demandes':
                return $this->statsAutresDemandes($request);
            default:
                return response()->json(['error' => 'Type non reconnu'], 400);
        }
    }

    private function statsRecouvrements(Request $request)
    {
        $query = DeclarationPcs::query();

        if ($request->filled('programme')) $query->where('programme', $request->programme);
        if ($request->filled('annee')) $query->where('annee', $request->annee);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        }
        if ($request->filled('poste_id')) $query->where('poste_id', $request->poste_id);
        if ($request->filled('mois')) $query->where('mois', $request->mois);

        $declarations = $query->get();

        return response()->json([
            'total' => $declarations->count(),
            'montant' => number_format($declarations->sum('montant_recouvrement'), 0, ',', ' '),
            'postes' => $declarations->pluck('poste_id')->unique()->filter()->count(),
        ]);
    }

    private function statsReversements(Request $request)
    {
        $query = DeclarationPcs::query();

        if ($request->filled('programme')) $query->where('programme', $request->programme);
        if ($request->filled('annee')) $query->where('annee', $request->annee);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut . ' 00:00:00', $request->date_fin . ' 23:59:59']);
        }
        if ($request->filled('poste_id')) $query->where('poste_id', $request->poste_id);
        if ($request->filled('mois')) $query->where('mois', $request->mois);

        $declarations = $query->get();

        return response()->json([
            'total' => $declarations->count(),
            'montant' => number_format($declarations->sum('montant_reversement'), 0, ',', ' '),
            'postes' => $declarations->pluck('poste_id')->unique()->filter()->count(),
        ]);
    }

    private function statsAutresDemandes(Request $request)
    {
        $query = AutreDemande::query();

        if ($request->filled('annee')) $query->where('annee', $request->annee);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_demande', [$request->date_debut, $request->date_fin]);
        }
        if ($request->filled('poste_id')) $query->where('poste_id', $request->poste_id);
        if ($request->filled('statut')) $query->where('statut', $request->statut);

        $demandes = $query->get();

        return response()->json([
            'total' => $demandes->count(),
            'montant_demande' => number_format($demandes->sum('montant'), 0, ',', ' '),
            'montant_accorde' => number_format($demandes->where('statut', 'valide')->sum('montant_accord'), 0, ',', ' '),
        ]);
    }

    /**
     * Récupérer les données UEMOA/AES dynamiques
     */
    public function getDonneesUemoaAes(Request $request)
    {
        $programme = $request->get('programme', 'UEMOA');
        $annee = $request->get('annee', date('Y'));

        // Récupérer uniquement les déclarations validées
        $declarations = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('programme', $programme)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Préparer les données par poste et par mois
        $recouvrements = [];
        $reversements = [];

        foreach ($declarations as $declaration) {
            $nomPoste = $declaration->poste_id
                ? $declaration->poste->nom
                : $declaration->bureauDouane->libelle;

            $mois = $declaration->mois;

            // Initialiser les tableaux pour ce poste s'ils n'existent pas
            if (!isset($recouvrements[$nomPoste])) {
                $recouvrements[$nomPoste] = array_fill(0, 13, 0); // 0-11 pour mois, 12 pour total
            }
            if (!isset($reversements[$nomPoste])) {
                $reversements[$nomPoste] = array_fill(0, 13, 0);
            }

            // Convertir en millions de FCFA
            $montantRecouvrement = $declaration->montant_recouvrement / 1000000;
            $montantReversement = $declaration->montant_reversement / 1000000;

            // Ajouter au mois (index 0-11)
            $recouvrements[$nomPoste][$mois - 1] += $montantRecouvrement;
            $reversements[$nomPoste][$mois - 1] += $montantReversement;

            // Ajouter au total (index 12)
            $recouvrements[$nomPoste][12] += $montantRecouvrement;
            $reversements[$nomPoste][12] += $montantReversement;
        }

        // Trier les postes par ordre alphabétique
        ksort($recouvrements);
        ksort($reversements);

        return response()->json([
            'recouvrements' => $recouvrements,
            'reversements' => $reversements,
            'titre' => $programme === 'UEMOA'
                ? "SITUATION MENSUELLE DES LIQUIDATIONS DES RECOUVREMENTS ET DES REVERSEMENTS DU PCS-UEMOA AU TITRE DE L'EXERCICE {$annee} (REGIONS)"
                : "SITUATION DES REVERSEMENTS DU PC-AES AU TITRE DE L'EXERCICE {$annee}",
            'programme' => $programme,
            'annee' => $annee
        ]);
    }
}

<?php

namespace App\Http\Controllers\TRIE;

use App\Http\Controllers\Controller;
use App\Models\CotisationTrie;
use App\Models\Poste;
use App\Models\BureauTrie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class EtatTrieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,acct')->except(['index']);
    }

    /**
     * Page d'accueil des états
     */
    public function index()
    {
        return view('trie.etats.index');
    }

    /**
     * État mensuel des paiements (comme image 1)
     * Affiche par POSTE (somme des bureaux) pour un mois donné
     */
    public function etatMensuel(Request $request)
    {
        $mois = $request->get('mois', date('n'));
        $annee = $request->get('annee', date('Y'));

        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $nomMois = $moisList[$mois];

        // Récupérer toutes les cotisations validées pour cette période
        $cotisations = CotisationTrie::with(['poste', 'bureauTrie'])
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Organiser les données par poste
        $donneesParPoste = [];
        
        foreach ($cotisations as $cotisation) {
            $posteNom = $cotisation->poste->nom;
            
            if (!isset($donneesParPoste[$posteNom])) {
                $donneesParPoste[$posteNom] = [
                    'nom' => $posteNom,
                    'recouvrement_courant' => 0,
                    'apurement' => 0,
                    'montant_total' => 0,
                    'references' => [],
                    'observations' => [],
                ];
            }

            $donneesParPoste[$posteNom]['recouvrement_courant'] += $cotisation->montant_cotisation_courante;
            $donneesParPoste[$posteNom]['apurement'] += $cotisation->montant_apurement;
            $donneesParPoste[$posteNom]['montant_total'] += $cotisation->montant_total;
            
            if ($cotisation->reference_paiement) {
                $ref = $cotisation->reference_paiement;
                if ($cotisation->date_paiement) {
                    $ref .= ' du ' . $cotisation->date_paiement->format('d/m/Y');
                }
                $donneesParPoste[$posteNom]['references'][] = $ref;
            }
            
            if ($cotisation->observation) {
                $donneesParPoste[$posteNom]['observations'][] = $cotisation->observation;
            }
        }

        // Trier par nom de poste
        ksort($donneesParPoste);

        // Calculer les totaux généraux
        $totalRecouvrement = array_sum(array_column($donneesParPoste, 'recouvrement_courant'));
        $totalApurement = array_sum(array_column($donneesParPoste, 'apurement'));
        $totalGeneral = array_sum(array_column($donneesParPoste, 'montant_total'));

        $pdf = PDF::loadView('trie.pdf.etat-mensuel', compact(
            'donneesParPoste',
            'mois',
            'annee',
            'nomMois',
            'totalRecouvrement',
            'totalApurement',
            'totalGeneral'
        ));

        $pdf->setPaper('A4', 'landscape');
        
        $filename = "Situation_Paiements_TRIE_CCIM_{$nomMois}_{$annee}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * État consolidé annuel (comme image 2)
     * Affiche par BUREAU et par MOIS pour une année
     */
    public function etatConsolide(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        // Récupérer toutes les cotisations validées pour cette année
        $cotisations = CotisationTrie::with(['poste', 'bureauTrie'])
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        // Organiser les données par poste et bureau
        $donneesParPoste = [];
        
        foreach ($cotisations as $cotisation) {
            $posteNom = $cotisation->poste->nom;
            $bureauNom = $cotisation->bureauTrie->nom_bureau;
            $mois = $cotisation->mois;

            if (!isset($donneesParPoste[$posteNom])) {
                $donneesParPoste[$posteNom] = [
                    'nom' => $posteNom,
                    'bureaux' => [],
                ];
            }

            if (!isset($donneesParPoste[$posteNom]['bureaux'][$bureauNom])) {
                $donneesParPoste[$posteNom]['bureaux'][$bureauNom] = [
                    'nom' => $bureauNom,
                    'mois' => array_fill(1, 12, 0), // Initialiser les 12 mois à 0
                    'total' => 0,
                ];
            }

            $donneesParPoste[$posteNom]['bureaux'][$bureauNom]['mois'][$mois] = $cotisation->montant_total;
            $donneesParPoste[$posteNom]['bureaux'][$bureauNom]['total'] += $cotisation->montant_total;
        }

        // Calculer les totaux mensuels
        $totauxMensuels = array_fill(1, 12, 0);
        $totalGeneral = 0;

        foreach ($donneesParPoste as &$poste) {
            foreach ($poste['bureaux'] as $bureau) {
                for ($m = 1; $m <= 12; $m++) {
                    $totauxMensuels[$m] += $bureau['mois'][$m];
                }
                $totalGeneral += $bureau['total'];
            }
        }

        // Récupérer les totaux de l'année précédente pour le tableau récapitulatif
        $anneePrecedente = $annee - 1;
        $totauxParPosteAnneePrecedente = $this->getTotauxParPoste($anneePrecedente);
        $totauxParPosteAnneeActuelle = $this->getTotauxParPoste($annee);

        // Trier par nom de poste
        ksort($donneesParPoste);

        $pdf = PDF::loadView('trie.pdf.etat-consolide', compact(
            'donneesParPoste',
            'annee',
            'anneePrecedente',
            'totauxMensuels',
            'totalGeneral',
            'totauxParPosteAnneePrecedente',
            'totauxParPosteAnneeActuelle'
        ));

        $pdf->setPaper('A4', 'landscape');
        
        $filename = "Situation_Cotisations_TRIE_CCIM_{$annee}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Obtenir les totaux par poste pour une année donnée
     */
    private function getTotauxParPoste($annee)
    {
        $cotisations = CotisationTrie::with('poste')
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();

        $totaux = [];

        foreach ($cotisations as $cotisation) {
            $posteNom = $cotisation->poste->nom;
            
            if (!isset($totaux[$posteNom])) {
                $totaux[$posteNom] = 0;
            }
            
            $totaux[$posteNom] += $cotisation->montant_total;
        }

        ksort($totaux);

        return $totaux;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use App\Notifications\DemandeFondsNotification;
use App\Models\User;
use App\Models\Poste;
use App\Notifications\DemandeFondsStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\View; // Pour utiliser la vue
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\DemandeFondsStatusUpdated; // Assurez-vous que le chemin est correct
use App\Notifications\DemandeStatusUpdated; // Assurez-vous que le chemin d'importation est correct



class DemandeFondsController extends Controller
{
    public function index(Request $request)
{
        $query = DemandeFonds::query();
    
        // Filtrer par référence si le champ est renseigné
        if ($request->filled('poste')) {
                $query->whereHas('poste', function($q) use ($request) {
                    $q->where('nom', 'like', '%' . $request->input('poste') . '%');
            });
        }
    
        // Filtrer par expéditeur si le champ est renseigné
        if ($request->filled('mois')) {
            $query->where('mois', 'like', '%' . $request->input('mois') . '%');
        }
    
        // Filtrer par montant total courant si le champ est renseigné
        if ($request->filled('total_courant')) {
            $query->where('total_courant', 'like', '%' . $request->input('total_courant') . '%');
        }
    
        // Filtrer par date d'arrivée si le champ est renseigné
       /*  if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        } */
    
        // Afficher toutes les demandes de fonds
        $demandeFonds = $query->with('user', 'poste')->orderBy('created_at', 'desc')->paginate(8);
         
        return view('demandes.index', compact('demandeFonds'));
    }
    

    public function create()
    {
        // Récupérer les postes pour le formulaire de création
        $postes = \App\Models\Poste::all();
        return view('demandes.create', compact('postes'));
    }

    public function store(Request $request)
{
    // Validation des données
    $request->validate([
        'date' => 'nullable|date',
        'date_reception' => 'nullable|date',    
        'mois' => 'nullable|string',
        'annee' => 'nullable|numeric',
        'poste_id' => 'nullable|integer',
        'status' => 'nullable|string',
        'fonctionnaires_bcs_net' => 'nullable|numeric',
        'fonctionnaires_bcs_revers' => 'nullable|numeric',
        'fonctionnaires_bcs_total_courant' => 'required|numeric',
        'fonctionnaires_bcs_salaire_ancien' => 'nullable|numeric',
        'fonctionnaires_bcs_total_demande' => 'nullable|numeric',
        'collectivite_sante_net' => 'nullable|numeric',
        'collectivite_sante_revers' => 'nullable|numeric',
        'collectivite_sante_total_courant' => 'nullable|numeric',
        'collectivite_sante_salaire_ancien' => 'nullable|numeric',
        'collectivite_sante_total_demande' => 'nullable|numeric',
        'collectivite_education_net' => 'nullable|numeric',
        'collectivite_education_revers' => 'nullable|numeric',
        'collectivite_education_total_courant' => 'nullable|numeric',
        'collectivite_education_salaire_ancien' => 'nullable|numeric',
        'collectivite_education_total_demande' => 'nullable|numeric',
        'personnels_saisonniers_net' => 'nullable|numeric',
        'personnels_saisonniers_revers' => 'nullable|numeric',
        'personnels_saisonniers_total_courant' => 'nullable|numeric',
        'personnels_saisonniers_salaire_ancien' => 'nullable|numeric',
        'personnels_saisonniers_total_demande' => 'nullable|numeric',
        'epn_net' => 'nullable|numeric',
        'epn_revers' => 'nullable|numeric',
        'epn_total_courant' => 'nullable|numeric',
        'epn_salaire_ancien' => 'nullable|numeric',
        'epn_total_demande' => 'nullable|numeric',
        'ced_net' => 'nullable|numeric',
        'ced_revers' => 'nullable|numeric',
        'ced_total_courant' => 'nullable|numeric',
        'ced_salaire_ancien' => 'nullable|numeric',
        'ced_total_demande' => 'nullable|numeric',
        'ecom_net' => 'nullable|numeric',
        'ecom_revers' => 'nullable|numeric',
        'ecom_total_courant' => 'nullable|numeric',
        'ecom_salaire_ancien' => 'nullable|numeric',
        'ecom_total_demande' => 'nullable|numeric',
        'cfp_cpam_net' => 'nullable|numeric',
        'cfp_cpam_revers' => 'nullable|numeric',
        'cfp_cpam_total_courant' => 'nullable|numeric',
        'cfp_cpam_salaire_ancien' => 'nullable|numeric',
        'cfp_cpam_total_demande' => 'nullable|numeric',
        'user_id' => 'required|integer',
        'montant_disponible' => 'nullable|numeric',
        'solde' => 'nullable|numeric',
        
    ]);

    // Calcul des totaux
    $total_net = 
        intval($request->fonctionnaires_bcs_net) +
        intval($request->collectivite_sante_net) +
        intval($request->collectivite_education_net) +
        intval($request->personnels_saisonniers_net) +
        intval($request->epn_net) +
        intval($request->ced_net) +
        intval($request->ecom_net) +
        intval($request->cfp_cpam_net);

    $total_revers = 
        intval($request->fonctionnaires_bcs_revers) +
        intval($request->collectivite_sante_revers) +
        intval($request->collectivite_education_revers) +
        intval($request->personnels_saisonniers_revers) +
        intval($request->epn_revers) +
        intval($request->ced_revers) +
        intval($request->ecom_revers) +
        intval($request->cfp_cpam_revers);

    $total_courant = 
        intval($request->fonctionnaires_bcs_total_courant) +
        intval($request->collectivite_sante_total_courant) +
        intval($request->collectivite_education_total_courant) +
        intval($request->personnels_saisonniers_total_courant) +
        intval($request->epn_total_courant) +
        intval($request->ced_total_courant) +
        intval($request->ecom_total_courant) +
        intval($request->cfp_cpam_total_courant);
    // Calcul du total demande
    $total_ancien = 
        $request->fonctionnaires_bcs_salaire_ancien +
        $request->collectivite_sante_salaire_ancien +
        $request->collectivite_education_salaire_ancien +
        $request->personnels_saisonniers_salaire_ancien +
        $request->epn_salaire_ancien +
        $request->ced_salaire_ancien +
        $request->ecom_salaire_ancien +
        $request->cfp_cpam_salaire_ancien;

        $request->merge([
            'total_net' => $total_net,
            'total_revers' => $total_revers,
            'total_courant' => $total_courant,
            'total_ancien' => $total_ancien,  // Ajoutez ceci
        ]);
    
    // Enregistrement dans la base de données
    DemandeFonds::create([
        'date' => $request->date,
        'date_reception' => $request->date_reception,
        'mois' => $request->mois,
        'annee' => $request->annee,
        'poste_id' => $request->poste_id,
        'status' => $request->status,
        'fonctionnaires_bcs_net' => $request->fonctionnaires_bcs_net,
        'fonctionnaires_bcs_revers' => $request->fonctionnaires_bcs_revers,
        'fonctionnaires_bcs_total_courant' => $request->fonctionnaires_bcs_total_courant,
        'fonctionnaires_bcs_salaire_ancien' => $request->fonctionnaires_bcs_salaire_ancien,
        'fonctionnaires_bcs_total_demande' => $request->fonctionnaires_bcs_total_demande,
        'collectivite_sante_net' => $request->collectivite_sante_net,
        'collectivite_sante_revers' => $request->collectivite_sante_revers,
        'collectivite_sante_total_courant' => $request->collectivite_sante_total_courant,
        'collectivite_sante_salaire_ancien' => $request->collectivite_sante_salaire_ancien,
        'collectivite_sante_total_demande' => $request->collectivite_sante_total_demande,
        'collectivite_education_net' => $request->collectivite_education_net,
        'collectivite_education_revers' => $request->collectivite_education_revers,
        'collectivite_education_total_courant' => $request->collectivite_education_total_courant,
        'collectivite_education_salaire_ancien' => $request->collectivite_education_salaire_ancien,
        'collectivite_education_total_demande' => $request->collectivite_education_total_demande,
        'personnels_saisonniers_net' => $request->personnels_saisonniers_net,
        'personnels_saisonniers_revers' => $request->personnels_saisonniers_revers,
        'personnels_saisonniers_total_courant' => $request->personnels_saisonniers_total_courant,
        'personnels_saisonniers_salaire_ancien' => $request->personnels_saisonniers_salaire_ancien,
        'personnels_saisonniers_total_demande' => $request->personnels_saisonniers_total_demande,
        'epn_net' => $request->epn_net,
        'epn_revers' => $request->epn_revers,
        'epn_total_courant' => $request->epn_total_courant,
        'epn_salaire_ancien' => $request->epn_salaire_ancien,
        'epn_total_demande' => $request->epn_total_demande,
        'ced_net' => $request->ced_net,
        'ced_revers' => $request->ced_revers,
        'ced_total_courant' => $request->ced_total_courant,
        'ced_salaire_ancien' => $request->ced_salaire_ancien,
        'ced_total_demande' => $request->ced_total_demande,
        'ecom_net' => $request->ecom_net,
        'ecom_revers' => $request->ecom_revers,
        'ecom_total_courant' => $request->ecom_total_courant,
        'ecom_salaire_ancien' => $request->ecom_salaire_ancien,
        'ecom_total_demande' => $request->ecom_total_demande,
        'cfp_cpam_net' => $request->cfp_cpam_net,
        'cfp_cpam_revers' => $request->cfp_cpam_revers,
        'cfp_cpam_total_courant' => $request->cfp_cpam_total_courant,
        'cfp_cpam_salaire_ancien' => $request->cfp_cpam_salaire_ancien,
        'cfp_cpam_total_demande' => $request->cfp_cpam_total_demande,
        'total_net' => $total_net,
        'total_revers' => $total_revers,
        'total_courant' => $total_courant,
        'total_ancien' => $total_ancien,
        'user_id' => $request->user_id,
        'montant_disponible' => $request->montant_disponible,
        'solde' => $request->solde
    ]);
// Récupérer les utilisateurs avec le rôle "acct"
/* $users = User::role('acct')->get(); */

// Assurez-vous que $demandeFonds est défini avant la boucle
/* $demandeFonds = DemandeFonds::latest()->first(); // Initialisation appropriée de $demandeFonds

// Envoyer la notification
foreach ($users as $user) {
    $user->notify(new DemandeFondsNotification($demandeFonds));
} */
Alert::success('Success', 'Demande de fonds créée avec succès.');
return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds créée avec succès.');
}

    

    public function edit($id)
    {
        // Récupérer les postes pour le formulaire de modification
        $demande = DemandeFonds::findOrFail($id);
        $postes = \App\Models\Poste::all();
        
        return view('demandes.edit', compact('demande', 'postes'));
    }

      public function update(Request $request, DemandeFonds $demandeFonds)
    {
        // Valider les champs de la requête
        $request->validate([
            'mois' => 'nullable|string|max:20',
            'annee' => 'nullable|numeric',
            'total_demande' => 'nullable|numeric',
            'status' => 'nullable|string',
            'fonctionnaires_bcs_net' => 'nullable|numeric',
            'fonctionnaires_bcs_revers' => 'nullable|numeric',
            'fonctionnaires_bcs_total_courant' => 'nullable|numeric',
            'fonctionnaires_bcs_salaire_ancien' => 'nullable|numeric',
            'fonctionnaires_bcs_total_demande' => 'nullable|numeric',
            'collectivite_sante_net' => 'nullable|numeric',
            'collectivite_sante_revers' => 'nullable|numeric',
            'collectivite_sante_total_courant' => 'nullable|numeric',
            'collectivite_sante_salaire_ancien' => 'nullable|numeric',
            'collectivite_sante_total_demande' => 'nullable|numeric',
            'collectivite_education_net' => 'nullable|numeric',
            'collectivite_education_revers' => 'nullable|numeric',
            'collectivite_education_total_courant' => 'nullable|numeric',
            'collectivite_education_salaire_ancien' => 'nullable|numeric',
            'collectivite_education_total_demande' => 'nullable|numeric',
            'personnels_saisonniers_net' => 'nullable|numeric',
            'personnels_saisonniers_revers' => 'nullable|numeric',
            'personnels_saisonniers_total_courant' => 'nullable|numeric',
            'personnels_saisonniers_salaire_ancien' => 'nullable|numeric',
            'personnels_saisonniers_total_demande' => 'nullable|numeric',
            'epn_net' => 'nullable|numeric',
            'epn_revers' => 'nullable|numeric',
            'epn_total_courant' => 'nullable|numeric',
            'epn_salaire_ancien' => 'nullable|numeric',
            'epn_total_demande' => 'nullable|numeric',
            'poste_id' => 'nullable|exists:postes,id',
            'date_reception' => 'nullable|date',
            'ced_net' => 'nullable|numeric',
            'ced_revers' => 'nullable|numeric',
            'ced_total_courant' => 'nullable|numeric',
            'ced_salaire_ancien' => 'nullable|numeric',
            'ced_total_demande' => 'nullable|numeric',
            'ecom_net' => 'nullable|numeric',
            'ecom_revers' => 'nullable|numeric',
            'ecom_total_courant' => 'nullable|numeric',
            'ecom_salaire_ancien' => 'nullable|numeric',
            'ecom_total_demande' => 'nullable|numeric',
            'cfp_cpam_net' => 'nullable|numeric',
            'cfp_cpam_revers' => 'nullable|numeric',
            'cfp_cpam_total_courant' => 'nullable|numeric',
            'cfp_cpam_salaire_ancien' => 'nullable|numeric',
            'cfp_cpam_total_demande' => 'nullable|numeric',
            'total_net' => 'nullable|numeric',
            'total_revers' => 'nullable|numeric',
            'total_courant' => 'nullable|numeric',
            'total_ancien' => 'nullable|numeric',
            'total_disponibilite' => 'nullable|numeric',
            'solde' => 'nullable|numeric',
        ]);

    
        // ... code existant ...
        Alert::success('Success', 'Demande de fonds mise à jour avec succès.');
        return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds mise à jour avec succès.'); 
    }  

    public function destroy(DemandeFonds $demandeFonds)
    {
        // Supprimer la demande de fonds
        $demandeFonds->delete();
        Alert::success('Success', 'Demande de fonds supprimée avec succès.');
        return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds supprimée avec succès.');
    }

    public function show($id)
    {
        // Récupération de la demande de fonds par ID
        $demandeFonds = DemandeFonds::with('poste')->findOrFail($id);
    
        // Calcul des totaux et des écarts
        $demandeFonds->total_net = $demandeFonds->fonctionnaires_bcs_net + 
                                   $demandeFonds->collectivite_sante_net + 
                                   $demandeFonds->collectivite_education_net + 
                                   $demandeFonds->personnels_saisonniers_net + 
                                   $demandeFonds->epn_net + 
                                   $demandeFonds->ced_net + 
                                   $demandeFonds->ecom_net + 
                                   $demandeFonds->cfp_cpam_net;
    
        $demandeFonds->total_revers = $demandeFonds->fonctionnaires_bcs_revers + 
                                       $demandeFonds->collectivite_sante_revers + 
                                       $demandeFonds->collectivite_education_revers + 
                                       $demandeFonds->personnels_saisonniers_revers + 
                                       $demandeFonds->epn_revers + 
                                       $demandeFonds->ced_revers + 
                                       $demandeFonds->ecom_revers + 
                                       $demandeFonds->cfp_cpam_revers;
    
        $demandeFonds->total_courant = $demandeFonds->fonctionnaires_bcs_total_courant + 
                                        $demandeFonds->collectivite_sante_total_courant + 
                                        $demandeFonds->collectivite_education_total_courant + 
                                        $demandeFonds->personnels_saisonniers_total_courant + 
                                        $demandeFonds->epn_total_courant + 
                                        $demandeFonds->ced_total_courant + 
                                        $demandeFonds->ecom_total_courant + 
                                        $demandeFonds->cfp_cpam_total_courant;
    
        $demandeFonds->total_ancien = $demandeFonds->fonctionnaires_bcs_salaire_ancien + 
                                       $demandeFonds->collectivite_sante_salaire_ancien + 
                                       $demandeFonds->collectivite_education_salaire_ancien + 
                                       $demandeFonds->personnels_saisonniers_salaire_ancien + 
                                       $demandeFonds->epn_salaire_ancien + 
                                       $demandeFonds->ced_salaire_ancien + 
                                       $demandeFonds->ecom_salaire_ancien + 
                                       $demandeFonds->cfp_cpam_salaire_ancien;
    
        return view('demandes.show', compact('demandeFonds'));
    }
        
    public function generatePDF($id)
        {
            // Récupérer la demande de fonds par son ID
            $demandeFonds = DemandeFonds::findOrFail($id);
        
            // Générer le PDF avec la vue et passer la variable
            $pdf = FacadePdf::loadView('pdf.demande_fonds', compact('demandeFonds'))->setPaper('a4', 'landscape');
            $demandeFonds->total_net = $demandeFonds->fonctionnaires_bcs_net + 
            $demandeFonds->collectivite_sante_net + 
            $demandeFonds->collectivite_education_net + 
            $demandeFonds->personnels_saisonniers_net + 
            $demandeFonds->epn_net + 
            $demandeFonds->ced_net + 
            $demandeFonds->ecom_net + 
            $demandeFonds->cfp_cpam_net;

    $demandeFonds->total_revers = $demandeFonds->fonctionnaires_bcs_revers + 
                $demandeFonds->collectivite_sante_revers + 
                $demandeFonds->collectivite_education_revers + 
                $demandeFonds->personnels_saisonniers_revers + 
                $demandeFonds->epn_revers + 
                $demandeFonds->ced_revers + 
                $demandeFonds->ecom_revers + 
                $demandeFonds->cfp_cpam_revers;

    $demandeFonds->total_courant = $demandeFonds->fonctionnaires_bcs_total_courant + 
                $demandeFonds->collectivite_sante_total_courant + 
                $demandeFonds->collectivite_education_total_courant + 
                $demandeFonds->personnels_saisonniers_total_courant + 
                $demandeFonds->epn_total_courant + 
                $demandeFonds->ced_total_courant + 
                $demandeFonds->ecom_total_courant + 
                $demandeFonds->cfp_cpam_total_courant;

    $demandeFonds->total_ancien = $demandeFonds->fonctionnaires_bcs_salaire_ancien + 
                $demandeFonds->collectivite_sante_salaire_ancien + 
                $demandeFonds->collectivite_education_salaire_ancien + 
                $demandeFonds->personnels_saisonniers_salaire_ancien + 
                $demandeFonds->epn_salaire_ancien + 
                $demandeFonds->ced_salaire_ancien + 
                $demandeFonds->ecom_salaire_ancien + 
                $demandeFonds->cfp_cpam_salaire_ancien;
            // Retourner le PDF pour le téléchargement
            return $pdf->download('demande_fonds_' . $demandeFonds->id . '.pdf');
        }

        public function updateStatus(Request $request, $id)
{
    $demande = DemandeFonds::findOrFail($id);
    
    // Vérifier si le statut est approuvé ou rejeté
    if ($request->status == 'approuve') {
        $demande->status = 'approuve';
        $demande->montant = $request->montant;
        $demande->observation = $request->observation;
    } elseif ($request->status == 'rejete') {
        $demande->status = 'rejete';
        $demande->observation = $request->observation; // Assurez-vous d'avoir un champ 'reason' dans votre table
    }

    // Enregistrer les modifications
    $demande->date_envois = $request->date_envois;
    $demande->save();

    return redirect()->route('demandes-fonds.envois')->with('success', 'Demande mise à jour avec succès');
}

public function EnvoisFonds(Request $request)
{
    // Commencer par obtenir toutes les demandes de fonds
    $query = DemandeFonds::with('user', 'poste');

    // Filtrer par poste si un poste est fourni dans la requête
    if ($request->filled('poste')) {
        $query->whereHas('poste', function ($q) use ($request) {
            $q->where('nom', 'like', '%' . $request->poste . '%');
        });
    }

    // Filtrer par mois si un mois est fourni dans la requête
    if ($request->filled('mois')) {
        $query->where('mois', 'like', '%' . $request->mois . '%');
    }

    // Filtrer par plage de dates (date d'envoi des demandes de fonds)
    if ($request->filled('date_debut') && $request->filled('date_fin')) {
        $query->whereBetween('date_envois', [$request->date_debut, $request->date_fin]);
    } elseif ($request->filled('date_debut')) {
        // Si seulement la date de début est fournie, filtrer à partir de cette date
        $query->where('date_envois', '>=', $request->date_debut);
    } elseif ($request->filled('date_fin')) {
        // Si seulement la date de fin est fournie, filtrer jusqu'à cette date
        $query->where('date_envois', '<=', $request->date_fin);
    }

    // Exécuter la requête et paginer les résultats
    $demandeFonds = $query->orderBy('created_at', 'desc')
        ->paginate(8)
        ->appends($request->except('page'));

    // Retourner la vue avec les résultats filtrés
    return view('demandes.envois', compact('demandeFonds'));
}


    public function SituationFonds(Request $request)
{
    // Commencer par obtenir toutes les demandes de fonds avec les statuts "approuvé" ou "rejeté"
    $query = DemandeFonds::with('user', 'poste')
        ->whereIn('status', ['approuve', 'rejete']);

    // Filtrer par poste si un poste est fourni dans la requête
    if ($request->filled('poste')) {
        $query->whereHas('poste', function ($q) use ($request) {
            $q->where('nom', 'like', '%' . $request->poste . '%');
        });
    }

    // Filtrer par mois si un mois est fourni dans la requête
    if ($request->filled('mois')) {
        $query->where('mois', 'like', '%' . $request->mois . '%');
    }

    // Filtrer par plage de dates (date d'envoi des demandes de fonds)
    if ($request->filled('date_debut') && $request->filled('date_fin')) {
        $query->whereBetween('date_envois', [$request->date_debut, $request->date_fin]);
    } elseif ($request->filled('date_debut')) {
        // Si seulement la date de début est fournie, filtrer à partir de cette date
        $query->where('date_envois', '>=', $request->date_debut);
    } elseif ($request->filled('date_fin')) {
        // Si seulement la date de fin est fournie, filtrer jusqu'à cette date
        $query->where('date_envois', '<=', $request->date_fin);
    }

    // Exécuter la requête et paginer les résultats
    $demandeFonds = $query->orderBy('created_at', 'desc')
        ->paginate(8)
        ->appends($request->except('page'));

    // Retourner la vue avec les résultats filtrés
    return view('demandes.situation', compact('demandeFonds'));
}

}
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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Policies\UserPolicy;
use Illuminate\Routing\Controller as BaseController; // Ajoutez cette ligne
use App\Http\Middleware\Rolemiddleware;

class DemandeFondsController extends Controller
{
    use AuthorizesRequests;

    private function authorizeRole(array $roles)
{
    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, '🚫 Accès refusé ! Vous n\'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter votre administrateur.');
    }
}


    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DemandeFonds::query();
        if ($user->role === 'tresorier') {
            $query->where('user_id', $user->id);
        }
        if ($request->filled('poste')) {
            $query->whereHas('poste', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('poste') . '%');
            });
        }

        // Filtrer par mois
        if ($request->filled('mois')) {
            $query->where('mois', 'like', '%' . $request->input('mois') . '%');
        }

        // Filtrer par montant total courant
        if ($request->filled('total_courant')) {
            $query->where('total_courant', 'like', '%' . $request->input('total_courant') . '%');
        }
        // Afficher toutes les demandes de fonds
        $demandeFonds = $query->with('user', 'poste')->orderBy('created_at', 'desc')->paginate(8);

        return view('demandes.index', compact('demandeFonds'));
    }


    public function create()
    {
        $this->authorizeRole(['tresorier', 'admin']);
        // Récupérer les postes pour le formulaire de création
        $postes = \App\Models\Poste::all();
        return view('demandes.create', compact('postes'));
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['tresorier', 'admin','']);
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

        $montant_disponible = $request->montant_disponible ?? 0; // Assurez-vous qu'il a une valeur par défaut
        $solde = $total_courant - $montant_disponible; // Calcul du solde

        $request->merge([
            'total_net' => $total_net,
            'total_revers' => $total_revers,
            'total_courant' => $total_courant,
            'total_ancien' => $total_ancien,
            'montant_disponible' => $montant_disponible,
            'solde' => $solde
        ]);

       
        // Enregistrement dans la base de données avec les valeurs calculées
        $demandeFonds = DemandeFonds::create($request->all());
        
        $acctUsers = User::whereIn('role', ['acct'])->get();
        foreach($acctUsers as $acctUser) {
            $acctUser->notify(new DemandeFondsNotification($demandeFonds));
        }
        Alert::success('Success', 'Demande de fonds créée avec succès.');
        return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds créée avec succès.');
    }


    public function edit($id)
    {
        $this->authorizeRole(['tresorier', 'admin']);
        // Récupérer les postes pour le formulaire de modification
        $demande = DemandeFonds::findOrFail($id);
        $postes = \App\Models\Poste::all();
        $users = User::all();
        return view('demandes.edit', compact('demande', 'postes', 'users'));
    }

    public function update(Request $request, DemandeFonds $demandeFonds)
    { 
        $this->authorizeRole(['tresorier', 'admin']);
        $user = Auth::user();

    // Vérifier si l'utilisateur est admin ou propriétaire de la demande
    if (!$user->isadmin() && $demandeFonds->user_id !== $user->id) {
        return redirect()->back()->withErrors(['error' => 'Vous n\'avez pas la permission de modifier cette demande.']);
    }
    // Empêcher la modification si la demande est déjà approuvée
    if ($demandeFonds->status === 'approuve') {
        return redirect()->back()->withErrors(['error' => 'Vous ne pouvez pas modifier une demande déjà approuvée.']);
    }
    
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
            'user_id' => 'nullable|exists:users,id',
            'poste_id' => 'nullable|exists:postes,id',
            'date_reception' => 'nullable|date'
        ]);

       
        $demandeFonds->update($request->all());
        // Rediriger avec un message de succès
        Alert::success('Success', 'Demande de fonds mise à jour avec succès.');
        return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds mise à jour avec succès.');
    }


    public function destroy(DemandeFonds $demandeFonds)
    {
        $this->authorizeRole(['admin']);
        // Supprimer la demande de fonds
        $demandeFonds->delete();
        Alert::success('Success', 'Demande de fonds supprimée avec succès.');
        return redirect()->route('demandes-fonds.index')->with('success', 'Demande de fonds supprimée avec succès.');
    }

    public function show($id)
    {
        $this->authorizeRole(['tresorier', 'admin', 'acct', 'superviseur']);
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

        $demandeFonds->solde = $demandeFonds->total_courant - $demandeFonds->montant_disponible;
        return view('demandes.show', compact('demandeFonds'));
    }

    public function generatePDF($id)
    {
        $this->authorizeRole(['tresorier', 'admin', 'acct', 'superviseur']);
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
        $demandeFonds->solde = $demandeFonds->total_courant - $demandeFonds->montant_disponible;
        return $pdf->download('demande_fonds_' . $demandeFonds->id . '.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeRole(['admin', 'acct']);
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
        $demande->user->notify(new DemandeFondsStatusNotification($demande));
       /*  $tresoriers = User::where('role', 'tresorier')->get();
        foreach($tresoriers as $tresorier) {
        if($tresorier->id !== auth::id()) {
        $tresorier->notify(new DemandeFondsStatusNotification($demande));
        }
        } */
        Alert::success('Success', 'Fonds envoyés avec succès');
        return redirect()->route('demandes-fonds.envois')->with('success', 'Fonds envoyés avec succès');
    }

    public function EnvoisFonds(Request $request)
    {
        $this->authorizeRole(['admin', 'acct']);
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
        // Obtenir l'utilisateur connecté
        $user = Auth::user();
    
        // Si l'utilisateur est trésorier, il ne voit que ses propres demandes
        if ($user->role === 'tresorier') {
            $query = DemandeFonds::with('user', 'poste')
                ->where('user_id', $user->id)
                ->whereIn('status', ['approuve', 'rejete']);
        } else {
            // Sinon, autoriser les autres rôles à voir toutes les demandes approuvées ou rejetées
            $query = DemandeFonds::with('user', 'poste')
                ->whereIn('status', ['approuve', 'rejete']);
        }
    
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
    

    public function SituationDF(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);

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
        return view('demandes.situationDF', compact('demandeFonds'));
    }
    public function SituationFE(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);
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
        return view('demandes.situationFE', compact('demandeFonds'));
    }

    public function Recap(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);
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
        return view('demandes.recap', compact('demandeFonds'));
    }
   
    public function Paiement(Request $request, $demande)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);
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
        return view('demandes.paiement', compact('demandeFonds'));
    }

    public function detail(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);
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
            $query->where('date_envois', '>=', $request->date_debut);
        } elseif ($request->filled('date_fin')) {
            $query->where('date_envois', '<=', $request->date_fin);
        }

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->appends($request->except('page'));

        // Assurez-vous de traiter chaque demande individuellement
        $typesFonctionnaires = [];
        foreach ($demandeFonds as $demande) {
            $typesFonctionnaires[] = [
                'BCS' => [
                    'designation' => 'Fonctionnaires BCS',
                    'net' => $demande->fonctionnaires_bcs_net,
                    'revers' => $demande->fonctionnaires_bcs_revers,
                    'total_courant' => $demande->fonctionnaires_bcs_total_courant,
                    'salaire_ancien' => $demande->fonctionnaires_bcs_salaire_ancien,
                ],
                'Collectivité Santé' => [
                    'designation' => 'Collectivité Santé',
                    'net' => $demande->collectivite_sante_net,
                    'revers' => $demande->collectivite_sante_revers,
                    'total_courant' => $demande->collectivite_sante_total_courant,
                    'salaire_ancien' => $demande->collectivite_sante_salaire_ancien,
                ],
                'Collectivité Education' => [
                    'designation' => 'Collectivité Education',
                    'net' => $demande->collectivite_education_net,
                    'revers' => $demande->collectivite_education_revers,
                    'total_courant' => $demande->collectivite_education_total_courant,
                    'salaire_ancien' => $demande->collectivite_education_salaire_ancien,
                ],
                'PersonnelsSaisonniers' => [
                    'designation' => 'Personnels Saisonniers',
                    'net' => $demande->personnels_saisonniers_net,
                    'revers' => $demande->personnels_saisonniers_revers,
                    'total_courant' => $demande->personnels_saisonniers_total_courant,
                    'salaire_ancien' => $demande->personnels_saisonniers_salaire_ancien,
                ],  
                'PersonnelsEpn' => [
                    'designation' => 'Personnels EPN',
                    'net' => $demande->epn_net,
                    'revers' => $demande->epn_revers,
                    'total_courant' => $demande->epn_total_courant,
                    'salaire_ancien' => $demande->epn_salaire_ancien,
                ],
                'PersonnelsCed' => [
                    'designation' => 'Personnels CED',
                    'net' => $demande->ced_net,
                    'revers' => $demande->ced_revers,
                    'total_courant' => $demande->ced_total_courant,
                    'salaire_ancien' => $demande->ced_salaire_ancien,
                ],
                'PersonnelsEcom' => [
                    'designation' => 'Personnels ECOM',
                    'net' => $demande->ecom_net,
                    'revers' => $demande->ecom_revers,
                    'total_courant' => $demande->ecom_total_courant,
                    'salaire_ancien' => $demande->ecom_salaire_ancien,
                ],
                'PersonnelsCfpCpam' => [
                    'designation' => 'Personnels CFPCPAM',
                    'net' => $demande->cfp_cpam_net,
                    'revers' => $demande->cfp_cpam_revers,
                    'total_courant' => $demande->cfp_cpam_total_courant,
                    'salaire_ancien' => $demande->cfp_cpam_salaire_ancien,
                ],
                // Ajoutez d'autres types de fonctionnaires ici si nécessaire
            ];
        }

        // Calcul du total global
            $totaux = [
                'net' => $demandeFonds->sum('total_net'),
                'revers' => $demandeFonds->sum('total_revers'),
                'total_courant' => $demandeFonds->sum('total_courant'),
                'total_ancien' => $demandeFonds->sum('total_ancien'),
                'total_ecart' => $demandeFonds->sum('total_ecart'),
            ];

        return view('demandes.detail', compact('typesFonctionnaires', 'totaux', 'demandeFonds'));
    }
    public function export(Request $request,)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);
        
        $demande = DemandeFonds::findOrFail($request->demande);
    
        $typesFonctionnaires = [
            'BCS' => [
                'designation' => 'Fonctionnaires BCS',
                'net' => $demande->fonctionnaires_bcs_net,
                'revers' => $demande->fonctionnaires_bcs_revers,
                'total_courant' => $demande->fonctionnaires_bcs_total_courant,
                'salaire_ancien' => $demande->fonctionnaires_bcs_salaire_ancien,
            ],
            'Collectivité Santé' => [
                'designation' => 'Collectivité Santé',
                'net' => $demande->collectivite_sante_net,
                'revers' => $demande->collectivite_sante_revers,
                'total_courant' => $demande->collectivite_sante_total_courant,
                'salaire_ancien' => $demande->collectivite_sante_salaire_ancien,
            ],
            'Collectivité Education' => [
                'designation' => 'Collectivité Education',
                'net' => $demande->collectivite_education_net,
                'revers' => $demande->collectivite_education_revers,
                'total_courant' => $demande->collectivite_education_total_courant,
                'salaire_ancien' => $demande->collectivite_education_salaire_ancien,
            ],
            'PersonnelsSaisonniers' => [
                'designation' => 'Personnels Saisonniers',
                'net' => $demande->personnels_saisonniers_net,
                'revers' => $demande->personnels_saisonniers_revers,
                'total_courant' => $demande->personnels_saisonniers_total_courant,
                'salaire_ancien' => $demande->personnels_saisonniers_salaire_ancien,
            ],  
            'PersonnelsEpn' => [
                'designation' => 'Personnels EPN',
                'net' => $demande->epn_net,
                'revers' => $demande->epn_revers,
                'total_courant' => $demande->epn_total_courant,
                'salaire_ancien' => $demande->epn_salaire_ancien,
            ],
            'PersonnelsCed' => [
                'designation' => 'Personnels CED',
                'net' => $demande->ced_net,
                'revers' => $demande->ced_revers,
                'total_courant' => $demande->ced_total_courant,
                'salaire_ancien' => $demande->ced_salaire_ancien,
            ],
            'PersonnelsEcom' => [
                'designation' => 'Personnels ECOM',
                'net' => $demande->ecom_net,
                'revers' => $demande->ecom_revers,
                'total_courant' => $demande->ecom_total_courant,
                'salaire_ancien' => $demande->ecom_salaire_ancien,
            ],
            'PersonnelsCfpCpam' => [
                'designation' => 'Personnels CFPCPAM',
                'net' => $demande->cfp_cpam_net,
                'revers' => $demande->cfp_cpam_revers,
                'total_courant' => $demande->cfp_cpam_total_courant,
                'salaire_ancien' => $demande->cfp_cpam_salaire_ancien,
            ],
            // ... (même structure que dans la méthode detail)
        ];
    
        $fileName = 'demande_fonds_' . $demande->id . '.csv';
    
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
    
        $columns = ['Désignation', 'Salaire Net', 'Revers/Salaire', 'Total mois courant', 'Salaire mois antérieur', 'Écart'];
    
        $callback = function() use($typesFonctionnaires, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach ($typesFonctionnaires as $type => $data) {
                fputcsv($file, [
                    $data['designation'],
                    $data['net'],
                    $data['revers'],
                    $data['total_courant'],
                    $data['salaire_ancien'],
                    $data['total_courant'] - $data['salaire_ancien']
                ]);
            }
    
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }
}

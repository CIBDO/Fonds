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
use Illuminate\Support\Facades\Validator;

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
        $demandeFonds = $query->with('user', 'poste')->orderBy('created_at', 'desc')->paginate(19);

        return view('demandes.index', compact('demandeFonds'));
    }

    public function create()
    {
        $this->authorizeRole(['tresorier', 'admin']);
        $postes = \App\Models\Poste::all();

        // Tableau des mois
        $mois = [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Août',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre'
        ];

        // Obtenir le mois actuel avec la première lettre en majuscule
        $currentMonthName = ucfirst(Carbon::now()->locale('fr')->translatedFormat('F'));
        $currentMonthIndex = array_search($currentMonthName, $mois);

        // Si l'index n'est pas trouvé, renvoyer une erreur
        if ($currentMonthIndex === false) {
            return back()->withErrors(['message' => 'Le mois actuel est invalide : ' . $currentMonthName]);
        }

        // Calculer l'index du mois précédent
        $previousMonthIndex = ($currentMonthIndex === 0) ? 11 : $currentMonthIndex - 1;
        $previousMonth = $mois[$previousMonthIndex];
        $previousYear = ($currentMonthIndex === 0) ? Carbon::now()->subYear()->format('Y') : Carbon::now()->format('Y');

        // Récupérer les données du mois précédent
        $previousData = DemandeFonds::where('mois', $previousMonth)
            ->where('annee', $previousYear)
            ->where('user_id', Auth::id())
            ->first();
        /*
        if (!$previousData) {
            return back()->withErrors(['message' => 'Aucune donnée trouvée pour le mois précédent (' . $previousMonth . ').']);
        }
 */
        return view('demandes.create', compact('postes', 'previousData'));
    }


    public function store(Request $request)
    {
        $this->authorizeRole(['tresorier', 'admin', '']);

        // Nettoyage des champs numériques pour retirer les espaces insécables et les convertir en nombres
        $cleanData = collect($request->all())->map(function ($value, $key) {
            // Si le champ est une chaîne, retirer les espaces insécables et convertir en nombre si possible
            if (is_string($value)) {
                $value = str_replace("\u{202F}", '', $value); // Retirer les espaces insécables
                $value = str_replace(' ', '', $value); // Retirer les espaces classiques
                if (is_numeric($value)) {
                    return (float) $value;
                }
            }
            return $value;
        })->toArray();

        // Remplacer les données de la requête par les données nettoyées
        $request->replace($cleanData);

        $request->validate([
            'date' => 'nullable|date',
            'date_reception' => 'nullable|date',
            'mois' => 'required|string',
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
            'montant_disponible' => 'required|numeric',
            'solde' => 'nullable|numeric',
        ]);

        /* $userId = $request->user_id; */
        $mois = $request->mois;
        $annee = $request->annee;
        $posteId = $request->poste_id;

        // Vérifier si une demande existe déjà
        $demandeExistante = DemandeFonds::where('poste_id', $posteId)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->exists();

        if ($demandeExistante) {
            session()->flash('message_erreur', 'Vous avez déjà fait une demande pour ce mois.');
            return redirect()->back();
        }

        // Calcul des totaux avec les valeurs nettoyées
        $total_net =
            floatval($request->fonctionnaires_bcs_net) +
            floatval($request->collectivite_sante_net) +
            floatval($request->collectivite_education_net) +
            floatval($request->personnels_saisonniers_net) +
            floatval($request->epn_net) +
            floatval($request->ced_net) +
            floatval($request->ecom_net) +
            floatval($request->cfp_cpam_net);

        $total_revers =
            floatval($request->fonctionnaires_bcs_revers) +
            floatval($request->collectivite_sante_revers) +
            floatval($request->collectivite_education_revers) +
            floatval($request->personnels_saisonniers_revers) +
            floatval($request->epn_revers) +
            floatval($request->ced_revers) +
            floatval($request->ecom_revers) +
            floatval($request->cfp_cpam_revers);

        $total_courant =
            floatval($request->fonctionnaires_bcs_total_courant) +
            floatval($request->collectivite_sante_total_courant) +
            floatval($request->collectivite_education_total_courant) +
            floatval($request->personnels_saisonniers_total_courant) +
            floatval($request->epn_total_courant) +
            floatval($request->ced_total_courant) +
            floatval($request->ecom_total_courant) +
            floatval($request->cfp_cpam_total_courant);

        $total_ancien =
            floatval($request->fonctionnaires_bcs_salaire_ancien) +
            floatval($request->collectivite_sante_salaire_ancien) +
            floatval($request->collectivite_education_salaire_ancien) +
            floatval($request->personnels_saisonniers_salaire_ancien) +
            floatval($request->epn_salaire_ancien) +
            floatval($request->ced_salaire_ancien) +
            floatval($request->ecom_salaire_ancien) +
            floatval($request->cfp_cpam_salaire_ancien);

        $montant_disponible = floatval($request->montant_disponible);
        $solde = $total_courant - $montant_disponible;

        // Mettre à jour la requête avec les totaux calculés
        $request->merge([
            'total_net' => $total_net,
            'total_revers' => $total_revers,
            'total_courant' => $total_courant,
            'total_ancien' => $total_ancien,
            'montant_disponible' => $montant_disponible,
            'solde' => $solde
        ]);

        // Créer la demande avec les données nettoyées
        $demandeFonds = DemandeFonds::create($request->all());

        // Notifications
        $acctUsers = User::whereIn('role', ['acct'])->get();
        foreach ($acctUsers as $acctUser) {
            $acctUser->notify(new DemandeFondsNotification($demandeFonds));
        }

        Alert::success('Success', 'Demande de fonds créée avec succès.');
        return redirect()->route('demandes-fonds.index')
            ->with('success', 'Demande de fonds créée avec succès.');
    }

    public function edit($id)
    {
        $this->authorizeRole(['tresorier', 'admin']);
        $demande = DemandeFonds::findOrFail($id);

        // Vérifier si l'utilisateur est admin ou propriétaire de la demande
        $user = Auth::user();
        if (!$user->isAdmin() && $demande->user_id !== $user->id) {
            return redirect()->back()->withErrors(['error' => 'Vous n\'avez pas la permission de modifier cette demande.']);
        }

        $postes = \App\Models\Poste::all();
        $users = User::all();
        return view('demandes.edit', compact('demande', 'postes', 'users'));
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeRole(['admin', 'acct']); // Vérification des rôles autorisés

        $demande = DemandeFonds::findOrFail($id);

        // Vérifier si la demande est déjà approuvée
        if ($demande->status === 'approuve') {
            return redirect()->route('demandes-fonds.envois')->with('error', 'Cette demande est déjà approuvée et ne peut plus être modifiée.');
        }

        // Retirer les espaces et s'assurer que le montant est numérique
        $montant = str_replace(' ', '', $request->input('montant'));
        if (!is_numeric($montant)) {
            return redirect()->back()->with('error', 'Le montant est invalide. Veuillez entrer un montant numérique.');
        }

        // Vérifier le statut et mettre à jour en conséquence
        if ($request->status == 'approuve') {
            $demande->status = 'approuve';
            $demande->montant = $montant;
            $demande->observation = $request->observation;
        } elseif ($request->status == 'rejete') {
            $demande->status = 'rejete';
            $demande->observation = $request->observation;
        }

        // Enregistrer la date d'envoi
        $demande->date_envois = $request->date_envois;

        // Enregistrer les modifications dans la base de données
        $demande->save();

        // Envoyer une notification à l'utilisateur
        $demande->user->notify(new DemandeFondsStatusNotification($demande));

        // Retourner avec un message de succès
        Alert::success('Succès', 'Fonds envoyés avec succès');
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
            ->paginate(19)
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
            ->paginate(19)
            ->appends($request->except('page'));

        // Retourner la vue avec les résultats filtrés
        return view('demandes.situation', compact('demandeFonds'));
    }


    public function SituationDF(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);

        // Requête initiale
        $query = DemandeFonds::with('user', 'poste')
            ->whereIn('status', ['approuve', 'rejete']);

        // Filtres
        if ($request->filled('poste')) {
            $query->whereHas('poste', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->poste . '%');
            });
        }
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        // Récupération des données
        $demandeFonds = $query->orderBy('created_at', 'desc')->paginate(8);

        // Calcul des totaux globaux
        $totalDemande = $query->sum('total_courant');
        $totalRecettes = $query->sum('montant_disponible');
        $totalSolde = $query->sum('solde');

        return view('demandes.situationDF', compact('demandeFonds', 'totalDemande', 'totalRecettes', 'totalSolde'));
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

        // Filtrer par année si une année est fournie dans la requête
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(19)
            ->appends($request->except('page'));

        // Calcul des totaux globaux
        $totalDemande = $query->sum('total_courant');
        $totalRecettes = $query->sum('montant_disponible');
        $totalSolde = $query->sum('montant');

        return view('demandes.situationFE', compact('demandeFonds', 'totalDemande', 'totalRecettes', 'totalSolde'));
    }

    public function Recap(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur']);

        // Initialiser la requête pour récupérer les demandes de fonds approuvées ou rejetées
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

        // Filtrer par année si une année est fournie dans la requête
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        // Exécuter la requête et récupérer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(8)
            ->appends($request->except('page'));

        // Calcul des totaux globaux
        $totalNet = $query->sum('total_net');
        $totalRevers = $query->sum('total_revers');
        $totalCourant = $query->sum('total_courant');
        $totalAncien = $query->sum('total_ancien');
        $totalEcart = $totalCourant - $totalAncien;

        // Retourner la vue avec les résultats filtrés et les totaux globaux
        return view('demandes.recap', compact(
            'demandeFonds',
            'totalNet',
            'totalRevers',
            'totalCourant',
            'totalEcart'
        ));
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

        $callback = function () use ($typesFonctionnaires, $columns) {
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

    public function Recettes(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur', 'tresorier']);
        // Commencer par obtenir toutes les demandes de fonds avec les statuts "approuvé" ou "rejeté"
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

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->except('page'));

        // Retourner la vue avec les résultats filtrés
        return view('demandes.recettes', compact('demandeFonds'));
    }

    public function Solde(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur', 'tresorier']);
        // Commencer par obtenir toutes les demandes de fonds avec les statuts "approuvé" ou "rejeté"
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

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->except('page'));

        // Retourner la vue avec les résultats filtrés
        return view('demandes.solde', compact('demandeFonds'));
    }

    private function cleanNumericInputs($inputs)
    {
        foreach ($inputs as $key => $value) {
            if (is_string($value) && (
                strpos($key, '_net') !== false ||
                strpos($key, '_revers') !== false ||
                strpos($key, '_total_courant') !== false ||
                strpos($key, '_salaire_ancien') !== false ||
                strpos($key, '_total_demande') !== false ||
                $key === 'montant_disponible' ||
                $key === 'solde'
            )) {
                $inputs[$key] = str_replace(' ', '', $value);
            }
        }
        return $inputs;
    }

    /* public function Fonctionnaires(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur', 'tresorier']);
        // Commencer par obtenir toutes les demandes de fonds avec les statuts "approuvé" ou "rejeté"
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

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(19)
            ->appends($request->except('page'));
        // Retourner la vue avec les résultats filtrés
        return view('demandes.fonctionnaires', compact('demandeFonds'));
    } */

    public function Fonctionnaires(Request $request)
    {
        $this->authorizeRole(['acct', 'admin', 'superviseur', 'tresorier']);

        // Initialiser la requête pour récupérer les demandes de fonds approuvées ou rejetées
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

        // Exécuter la requête et paginer les résultats
        $demandeFonds = $query->orderBy('created_at', 'desc')
            ->paginate(19)
            ->appends($request->except('page'));

        // Calcul des totaux globaux pour chaque rubrique
        $totalBCS = $query->sum('fonctionnaires_bcs_total_courant');
        $totalSante = $query->sum('collectivite_sante_total_courant');
        $totalEducation = $query->sum('collectivite_education_total_courant');
        $totalSaisonnier = $query->sum('personnels_saisonniers_total_courant');
        $totalEPN = $query->sum('epn_total_courant');
        $totalCED = $query->sum('ced_total_courant');
        $totalECOM = $query->sum('ecom_total_courant');
        $totalCFPCPAM = $query->sum('cfp_cpam_total_courant');

        // Retourner la vue avec les résultats filtrés et les totaux globaux
        return view('demandes.fonctionnaires', compact(
            'demandeFonds',
            'totalBCS',
            'totalSante',
            'totalEducation',
            'totalSaisonnier',
            'totalEPN',
            'totalCED',
            'totalECOM',
            'totalCFPCPAM'
        ));
    }

    public function totauxParMois(Request $request)
    {
        $this->authorizeRole(['tresorier', 'admin', 'acct', 'superviseur']);

        // Récupérer l'année sélectionnée ou utiliser l'année actuelle par défaut
        $annee = $request->input('annee', Carbon::now()->year);

        // Calculer les totaux par mois pour l'année sélectionnée
        $montantsParMois = DemandeFonds::select('mois')
            ->selectRaw('SUM(total_courant) as total_mois')
            ->where('annee', $annee) // Filtrer par année
            ->groupBy('mois')
            ->orderBy('mois')
            ->paginate(12);

        return view('demandes.totaux-par-mois', compact('montantsParMois', 'annee'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['tresorier', 'admin']);
        $user = Auth::user();
        $demandeFonds = DemandeFonds::findOrFail($id);

        // Vérifier si l'utilisateur est admin ou propriétaire de la demande
        if (!$user->isAdmin() && $demandeFonds->user_id !== $user->id) {
            return redirect()->back()->withErrors(['error' => 'Vous n\'avez pas la permission de modifier cette demande.']);
        }

        // Empêcher la modification si la demande est déjà approuvée
        if ($demandeFonds->status === 'approuve') {
            return redirect()->back()->withErrors(['error' => 'Vous ne pouvez pas modifier une demande déjà approuvée.']);
        }

        // Nettoyage des champs numériques pour retirer les espaces insécables et les convertir en nombres
        $cleanData = collect($request->all())->map(function ($value, $key) {
            // Si le champ est une chaîne, retirer les espaces insécables et convertir en nombre si possible
            if (is_string($value)) {
                $value = str_replace("\u{202F}", '', $value); // Retirer les espaces insécables
                $value = str_replace(' ', '', $value); // Retirer les espaces classiques
                if (is_numeric($value)) {
                    return (float) $value;
                }
            }
            return $value;
        })->toArray();

        // Remplacer les données nettoyées dans la requête
        $request->replace($cleanData);
        /* dd($cleanData); */
        // Valider les champs de la requête
        $validatedData = $request->validate([
            'mois' => 'required|string',
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
            'total_salaire_ancien' => 'nullable|numeric',
            'total_demande' => 'nullable|numeric',
            'montant_disponible' => 'required|numeric',
            'solde' => 'nullable|numeric',
            'user_id' => 'nullable|exists:users,id',
            'poste_id' => 'nullable|exists:postes,id',
            'date_reception' => 'nullable|date',
            'date' => 'required|date'
        ]);

        $demandeFonds->update($validatedData);
        Alert::success('Success', 'Demande de fonds mise à jour avec succès.');
        return redirect()->route('demandes-fonds.index');
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

}
<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\AutreDemande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\PcsAutreDemandeSoumise;
use App\Notifications\PcsAutreDemandeValidee;
use App\Notifications\PcsAutreDemandeRejetee;

class AutreDemandeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des autres demandes
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = AutreDemande::with(['poste', 'saisiPar', 'validePar'])
            ->orderBy('date_demande', 'desc');

        // Filtres
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Si l'utilisateur n'est pas valideur, voir uniquement ses demandes
        if (!$user->peut_valider_pcs) {
            $query->where('poste_id', $user->poste_id);
        }

        $demandes = $query->paginate(20);

        return view('pcs.autres-demandes.index', compact('demandes'));
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

        return view('pcs.autres-demandes.create', compact('poste'));
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier si c'est une demande unique (ancien format) ou multiple (nouveau format)
        if ($request->has('demandes')) {
            // Nouveau format : création multiple
            return $this->storeMultiple($request, $user);
        }

        // Ancien format : création unique (pour compatibilité)
        $validated = $request->validate([
            'designation' => 'required|string|max:500',
            'montant' => 'required|numeric|min:0',
            'observation' => 'nullable|string',
            'date_demande' => 'required|date',
            'annee' => 'required|integer|min:2020',
        ], [
            'designation.required' => 'La désignation est obligatoire',
            'montant.required' => 'Le montant est obligatoire',
            'montant.numeric' => 'Le montant doit être un nombre',
            'date_demande.required' => 'La date de demande est obligatoire',
            'annee.required' => 'L\'année est obligatoire',
        ]);

        $demande = AutreDemande::create([
            'poste_id' => $user->poste_id,
            'designation' => $validated['designation'],
            'montant' => $validated['montant'],
            'observation' => $validated['observation'] ?? null,
            'date_demande' => $validated['date_demande'],
            'annee' => $validated['annee'],
            'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
            'saisi_par' => $user->id,
        ]);

        // Envoyer notification si soumission
        if ($request->input('action') === 'soumettre') {
            $this->envoyerNotificationSoumission($demande);
        }

        Alert::success('Succès', 'Demande enregistrée avec succès');
        return redirect()->route('pcs.autres-demandes.index');
    }

    /**
     * Enregistrement de plusieurs demandes à la fois
     */
    private function storeMultiple(Request $request, $user)
    {
        // Validation des données globales
        $request->validate([
            'annee_globale' => 'required|integer|min:2020',
            'date_globale' => 'required|date',
            'demandes' => 'required|array|min:1',
            'demandes.*.designation' => 'required|string|max:500',
            'demandes.*.montant' => 'required|numeric|min:0',
            'demandes.*.observation' => 'nullable|string',
        ], [
            'annee_globale.required' => 'L\'année est obligatoire',
            'date_globale.required' => 'La date est obligatoire',
            'demandes.required' => 'Au moins une demande est requise',
            'demandes.*.designation.required' => 'La désignation est obligatoire pour chaque demande',
            'demandes.*.montant.required' => 'Le montant est obligatoire pour chaque demande',
            'demandes.*.montant.numeric' => 'Le montant doit être un nombre',
            'demandes.*.montant.min' => 'Le montant doit être positif',
        ]);

        $statut = $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon';
        $demandesCreees = [];
        $erreurs = [];

        // Créer chaque demande
        foreach ($request->input('demandes') as $index => $demandeData) {
            try {
                $demande = AutreDemande::create([
                    'poste_id' => $user->poste_id,
                    'designation' => $demandeData['designation'],
                    'montant' => $demandeData['montant'],
                    'observation' => $demandeData['observation'] ?? null,
                    'date_demande' => $request->input('date_globale'),
                    'annee' => $request->input('annee_globale'),
                    'statut' => $statut,
                    'saisi_par' => $user->id,
                ]);

                $demandesCreees[] = $demande;

                // Envoyer notification si soumission
                if ($statut === 'soumis') {
                    $this->envoyerNotificationSoumission($demande);
                }
            } catch (\Exception $e) {
                $erreurs[] = "Erreur lors de la création de la demande #" . ($index + 1) . ": " . $e->getMessage();
            }
        }

        // Message de succès
        $nombreCreees = count($demandesCreees);
        if ($nombreCreees > 0) {
            $message = $nombreCreees === 1
                ? '1 demande enregistrée avec succès'
                : $nombreCreees . ' demandes enregistrées avec succès';

            if ($statut === 'soumis') {
                $message .= ' et soumise(s) pour validation';
            }

            Alert::success('Succès', $message);
        }

        // Afficher les erreurs s'il y en a
        if (count($erreurs) > 0) {
            foreach ($erreurs as $erreur) {
                Alert::warning('Attention', $erreur);
            }
        }

        return redirect()->route('pcs.autres-demandes.index');
    }

    /**
     * Affichage d'une demande
     */
    public function show(AutreDemande $demande)
    {
        $demande->load(['poste', 'saisiPar', 'validePar']);
        return view('pcs.autres-demandes.show', compact('demande'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(AutreDemande $demande)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que c'est bien l'utilisateur qui a créé la demande
        if ($demande->saisi_par !== $user->id && !$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous ne pouvez pas modifier cette demande');
            return redirect()->back();
        }

        // Ne peut être modifiée que si en brouillon
        if ($demande->statut !== 'brouillon') {
            Alert::error('Erreur', 'Seules les demandes en brouillon peuvent être modifiées');
            return redirect()->back();
        }

        return view('pcs.autres-demandes.edit', compact('demande'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, AutreDemande $demande)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:500',
            'montant' => 'required|numeric|min:0',
            'observation' => 'nullable|string',
            'date_demande' => 'required|date',
            'annee' => 'required|integer|min:2020',
        ]);

        $demande->update([
            'designation' => $validated['designation'],
            'montant' => $validated['montant'],
            'observation' => $validated['observation'] ?? null,
            'date_demande' => $validated['date_demande'],
            'annee' => $validated['annee'],
            'statut' => $request->input('action') === 'soumettre' ? 'soumis' : 'brouillon',
        ]);

        Alert::success('Succès', 'Demande modifiée avec succès');
        return redirect()->route('pcs.autres-demandes.index');
    }

    /**
     * Suppression
     */
    public function destroy(AutreDemande $demande)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Seul le créateur ou un valideur peut supprimer
        if ($demande->saisi_par !== $user->id && !$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous ne pouvez pas supprimer cette demande');
            return redirect()->back();
        }

        // Ne peut être supprimée que si en brouillon
        if ($demande->statut !== 'brouillon') {
            Alert::error('Erreur', 'Seules les demandes en brouillon peuvent être supprimées');
            return redirect()->back();
        }

        $demande->delete();
        Alert::success('Succès', 'Demande supprimée avec succès');

        return redirect()->route('pcs.autres-demandes.index');
    }

    /**
     * Validation d'une demande
     */
    public function valider(Request $request, AutreDemande $demande)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->peut_valider_pcs) {
            Alert::error('Erreur', 'Vous n\'avez pas l\'autorisation de valider');
            return redirect()->back();
        }

        $request->validate([
            'montant_accord' => 'required|numeric|min:0',
        ], [
            'montant_accord.required' => 'Le montant accordé est obligatoire',
            'montant_accord.numeric' => 'Le montant accordé doit être un nombre',
            'montant_accord.min' => 'Le montant accordé ne peut pas être négatif',
        ]);

        $demande->valider($user->id, $request->montant_accord);

        // Notifier le demandeur
        if ($demande->saisiPar) {
            $demande->saisiPar->notify(new PcsAutreDemandeValidee($demande));
        }

        $message = $request->montant_accord == $demande->montant
            ? 'Demande validée avec le montant complet'
            : "Demande validée avec un montant de " . number_format($request->montant_accord, 0, ',', ' ') . " FCFA (demandé: " . number_format($demande->montant, 0, ',', ' ') . " FCFA)";

        Alert::success('Succès', $message);

        return redirect()->back();
    }

    /**
     * Rejet d'une demande
     */
    public function rejeter(Request $request, AutreDemande $demande)
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

        $demande->rejeter($user->id, $request->motif_rejet);

        // Notifier le demandeur
        if ($demande->saisiPar) {
            $demande->saisiPar->notify(new PcsAutreDemandeRejetee($demande));
        }

        Alert::success('Succès', 'Demande rejetée');

        return redirect()->back();
    }

    /**
     * Statistiques par poste
     */
    public function statistiques(Request $request)
    {
        $annee = $request->input('annee', date('Y'));

        $stats = AutreDemande::selectRaw('poste_id, COUNT(*) as nombre, SUM(montant) as total_montant, SUM(montant_accord) as total_montant_accord')
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->groupBy('poste_id')
            ->with('poste')
            ->get();

        return view('pcs.autres-demandes.statistiques', compact('stats', 'annee'));
    }

    /**
     * Envoyer notification de soumission aux validateurs
     */
    private function envoyerNotificationSoumission($demande)
    {
        // Récupérer tous les utilisateurs qui peuvent valider les demandes PCS
        $validateurs = User::where('peut_valider_pcs', true)->get();

        // Envoyer notification à chaque validateur
        foreach ($validateurs as $validateur) {
            $validateur->notify(new PcsAutreDemandeSoumise($demande));
        }
    }
}


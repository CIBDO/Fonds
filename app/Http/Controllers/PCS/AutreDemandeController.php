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
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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

        // ACCT et admin voient toutes les demandes ; les autres voient uniquement leur poste
        $estValideurOuAcct = $user->peut_valider_pcs || $user->hasRole('acct') || $user->hasRole('admin');
        if (!$estValideurOuAcct) {
            $query->where('poste_id', $user->poste_id);
        }

        $demandes = $query->paginate(12);

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

        // Seul le créateur (poste émetteur) peut modifier la demande
        if ($demande->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs demandes');
            return redirect()->back();
        }

        // Ne peut être modifiée que si en brouillon, soumise ou rejetée (pas si validée)
        if (!in_array($demande->statut, ['brouillon', 'soumis', 'rejete'])) {
            Alert::error('Erreur', 'Seules les demandes en brouillon, soumises ou rejetées peuvent être modifiées');
            return redirect()->back();
        }

        return view('pcs.autres-demandes.edit', compact('demande'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, AutreDemande $demande)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Seul le créateur (poste émetteur) peut modifier la demande
        if ($demande->saisi_par !== $user->id) {
            Alert::error('Erreur', 'Seuls les postes émetteurs peuvent modifier leurs demandes');
            return redirect()->back();
        }

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

        // Seul le créateur ou un valideur (ACCT/admin) peut supprimer
        $estValideurOuAcct = $user->peut_valider_pcs || $user->hasRole('acct') || $user->hasRole('admin');
        if ($demande->saisi_par !== $user->id && !$estValideurOuAcct) {
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

        $estValideurOuAcct = $user->peut_valider_pcs || $user->hasRole('acct') || $user->hasRole('admin');
        if (!$estValideurOuAcct) {
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

        $estValideurOuAcct = $user->peut_valider_pcs || $user->hasRole('acct') || $user->hasRole('admin');
        if (!$estValideurOuAcct) {
            Alert::error('Erreur', 'Vous n\'avez pas l\'autorisation de rejeter');
            return redirect()->back();
        }

        $demande->rejeter($user->id, $request->motif_rejet);

        // Recharger la demande avec les relations pour la notification
        $demande->load(['poste', 'saisiPar']);

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
     * Envoyer notification de soumission aux validateurs (ACCT)
     */
    private function envoyerNotificationSoumission($demande)
    {
        // Récupérer tous les utilisateurs ACCT pour les notifier
        $acctUsers = User::whereIn('role', ['acct', 'admin'])->get();

        // Envoyer notification à chaque utilisateur ACCT
        foreach ($acctUsers as $acctUser) {
            $acctUser->notify(new PcsAutreDemandeSoumise($demande));
        }
    }

    /**
     * Générer l'état consolidé des autres demandes PCS
     */
    public function etatConsolideAutresDemandes(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        // Récupérer les données des autres demandes
        $autresDemandes = AutreDemande::with('poste')
            ->where('annee', $annee)
            ->get();

        // Organiser les données par poste et mois
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

            // Récapitulatif par poste
            $recapitulatifParPoste[$nomPoste]['montant_demande'] += $demande->montant;
            $recapitulatifParPoste[$nomPoste]['montant_accord'] += $demande->montant_accord ?? 0;
            $recapitulatifParPoste[$nomPoste]['dernier_mois'] = \Carbon\Carbon::parse($demande->date_demande)->format('m/Y');

            // Déterminer le statut principal du poste
            if ($demande->statut === 'valide') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'VALIDÉ';
            } elseif ($demande->statut === 'soumis') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'SOUMIS';
            } elseif ($demande->statut === 'rejete') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'REJETÉ';
            }
        }

        // Calculer les pourcentages
        foreach ($recapitulatifParPoste as $nomPoste => $data) {
            $pourcentage = $data['montant_demande'] > 0
                ? round(($data['montant_accord'] / $data['montant_demande']) * 100, 1)
                : 0;
            $recapitulatifParPoste[$nomPoste]['pourcentage_accord'] = $pourcentage;
        }

        // Totaux généraux
        $totalGeneral['montant_demande'] = $totalDemandesSoumises;
        $totalGeneral['montant_accord'] = $totalDemandesValidees;
        $totalGeneral['pourcentage_accord'] = $totalDemandesSoumises > 0
            ? round(($totalDemandesValidees / $totalDemandesSoumises) * 100, 1)
            : 0;

        // Trier par ordre alphabétique
        ksort($demandesSoumisesParPoste);
        ksort($demandesValideesParPoste);
        ksort($recapitulatifParPoste);

        $pdf = PDF::loadView('pcs.pdf.etat-autres-demandes-consolide', compact(
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
     * Afficher la vue de filtrage pour les états consolidés
     */
    public function filtreEtat()
    {
        $postes = \App\Models\Poste::orderBy('nom')->get();
        return view('pcs.autres-demandes.filtre-etat', compact('postes'));
    }

    /**
     * Générer l'état consolidé avec filtres personnalisés
     */
    public function etatConsolideFiltre(Request $request)
    {
        // Validation des paramètres
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'annee' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'poste_id' => 'nullable|exists:postes,id',
            'statut' => 'nullable|in:brouillon,soumis,valide,rejete',
            'format' => 'nullable|in:pdf,excel'
        ]);

        $dateDebut = $validated['date_debut'];
        $dateFin = $validated['date_fin'];
        $annee = $validated['annee'];
        $posteId = $validated['poste_id'];
        $statut = $validated['statut'];
        $format = $validated['format'] ?? 'pdf';

        // Construire la requête avec filtres
        $query = AutreDemande::with('poste')
            ->whereBetween('date_demande', [$dateDebut, $dateFin])
            ->where('annee', $annee);

        if ($posteId) {
            $query->where('poste_id', $posteId);
        }

        if ($statut) {
            $query->where('statut', $statut);
        }

        $autresDemandes = $query->get();

        // Organiser les données (même logique que etatConsolideAutresDemandes)
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

            // Récapitulatif par poste
            $recapitulatifParPoste[$nomPoste]['montant_demande'] += $demande->montant;
            $recapitulatifParPoste[$nomPoste]['montant_accord'] += $demande->montant_accord ?? 0;
            $recapitulatifParPoste[$nomPoste]['dernier_mois'] = \Carbon\Carbon::parse($demande->date_demande)->format('m/Y');

            // Déterminer le statut principal du poste
            if ($demande->statut === 'valide') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'VALIDÉ';
            } elseif ($demande->statut === 'soumis') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'SOUMIS';
            } elseif ($demande->statut === 'rejete') {
                $recapitulatifParPoste[$nomPoste]['statut'] = 'REJETÉ';
            }
        }

        // Calculer les pourcentages
        foreach ($recapitulatifParPoste as $nomPoste => $data) {
            $pourcentage = $data['montant_demande'] > 0
                ? round(($data['montant_accord'] / $data['montant_demande']) * 100, 1)
                : 0;
            $recapitulatifParPoste[$nomPoste]['pourcentage_accord'] = $pourcentage;
        }

        // Totaux généraux
        $totalGeneral['montant_demande'] = $totalDemandesSoumises;
        $totalGeneral['montant_accord'] = $totalDemandesValidees;
        $totalGeneral['pourcentage_accord'] = $totalDemandesSoumises > 0
            ? round(($totalDemandesValidees / $totalDemandesSoumises) * 100, 1)
            : 0;

        // Trier par ordre alphabétique
        ksort($demandesSoumisesParPoste);
        ksort($demandesValideesParPoste);
        ksort($recapitulatifParPoste);

        // Générer le nom du fichier avec les filtres
        $nomFichier = "Etat_Autres_Demandes_PCS_{$annee}";
        if ($posteId) {
            $poste = \App\Models\Poste::find($posteId);
            $nomFichier .= "_" . strtoupper($poste->nom);
        }
        if ($statut) {
            $nomFichier .= "_" . strtoupper($statut);
        }
        $nomFichier .= "_" . str_replace('-', '', $dateDebut) . "_" . str_replace('-', '', $dateFin);

        if ($format === 'pdf') {
            $pdf = PDF::loadView('pcs.pdf.etat-autres-demandes-consolide', compact(
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
        $query = AutreDemande::query();

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_demande', [$request->date_debut, $request->date_fin]);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->get();

        return response()->json([
            'total_demandes' => $demandes->count(),
            'montant_total' => number_format($demandes->sum('montant'), 0, ',', ' ') . ' FCFA',
            'demandes_soumises' => $demandes->where('statut', 'soumis')->count(),
            'demandes_validees' => $demandes->where('statut', 'valide')->count(),
        ]);
    }

    /**
     * Aperçu des données filtrées
     */
    public function apercu(Request $request)
    {
        $query = AutreDemande::with('poste');

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_demande', [$request->date_debut, $request->date_fin]);
        }

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        if ($request->filled('poste_id')) {
            $query->where('poste_id', $request->poste_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->orderBy('date_demande', 'desc')->limit(50)->get();

        if ($request->filled('apercu')) {
            return view('pcs.autres-demandes.apercu', compact('demandes'))->render();
        }

        return view('pcs.autres-demandes.apercu', compact('demandes'));
    }

    /**
     * Générer l'état consolidé des autres demandes pour un poste émetteur
     */
    public function etatConsolidePosteEmetteur(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->poste_id) {
            Alert::error('Erreur', 'Aucun poste assigné à votre compte');
            return redirect()->route('pcs.autres-demandes.index');
        }

        $annee = $request->get('annee', date('Y'));
        $poste = $user->poste;

        // Récupérer les demandes du poste émetteur
        $demandes = AutreDemande::where('poste_id', $poste->id)
            ->whereYear('date_demande', $annee)
            ->get();

        // Organiser les données par mois
        // Pour le poste émetteur : compter toutes les demandes créées pour le montant demandé
        $demandesSoumisesParMois = array_fill(1, 12, 0);
        $demandesValideesParMois = array_fill(1, 12, 0);
        $totalDemandesSoumises = 0;
        $totalDemandesValidees = 0;
        $montantSoumisParMois = array_fill(1, 12, 0);
        $montantValideParMois = array_fill(1, 12, 0);
        $totalMontantSoumis = 0;
        $totalMontantValide = 0;

        foreach ($demandes as $demande) {
            $mois = $demande->date_demande->month;

            // Pour le poste émetteur : compter TOUTES les demandes créées (tous statuts) pour le montant demandé
            // et le nombre de demandes
            $demandesSoumisesParMois[$mois]++;
            $totalDemandesSoumises++;
            $montantSoumisParMois[$mois] += $demande->montant;
            $totalMontantSoumis += $demande->montant;

            // Montant accordé : seulement pour les demandes validées
            if ($demande->statut === 'valide') {
                $demandesValideesParMois[$mois]++;
                $totalDemandesValidees++;
                $montantValideParMois[$mois] += $demande->montant_accord ?? $demande->montant;
                $totalMontantValide += $demande->montant_accord ?? $demande->montant;
            }
        }

        $pdf = PDF::loadView('pcs.pdf.etat-autres-demandes-consolide-poste-emetteur', compact(
            'demandesSoumisesParMois',
            'demandesValideesParMois',
            'totalDemandesSoumises',
            'totalDemandesValidees',
            'montantSoumisParMois',
            'montantValideParMois',
            'totalMontantSoumis',
            'totalMontantValide',
            'annee',
            'poste'
        ));

        return $pdf->download("Etat_Autres_Demandes_PCS_{$poste->nom}_{$annee}.pdf");
    }
}


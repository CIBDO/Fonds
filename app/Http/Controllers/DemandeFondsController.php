<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class DemandeFondsController extends Controller
{
    public function index()
    {
        // Afficher toutes les demandes de fonds
        $demandes = DemandeFonds::with('user', 'poste')->get();
        return view('demandes.index', compact('demandes'));
    }

    public function create()
    {
        // Récupérer les postes pour le formulaire de création
        $postes = \App\Models\Poste::all();
        return view('demandes.create', compact('postes'));
    }

    public function store(Request $request)
    {
        // Valider les champs de la requête
        $request->validate([
            'mois' => 'required|string|max:20',
            'annee' => 'required|numeric',
            'total_demande' => 'required|numeric',
            'status' => 'required|string',
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
            'poste_id' => 'required|exists:postes,id',
            'date_reception' => 'required|date',
        ]);
             // Calculer le mois et l'année du mois précédent
    $date = Carbon::parse($request->date); // Date de la demande
    $previousMonth = $date->copy()->subMonth(); // Soustraire un mois
    $previousMonthYear = $previousMonth->year;
    $previousMonthNumber = $previousMonth->month;

    // Récupérer les salaires du mois précédent
    $salariesAntérieurs = DemandeFonds::where('annee', $previousMonthYear)
        ->where('mois', $previousMonthNumber)
        ->first();

    // Vérifiez si des salaires existent, sinon définissez-les à zéro
    $salaireMoisAntérieur = [
        'fonctionnaires_bcs' => $salariesAntérieurs ? $salariesAntérieurs->fonctionnaires_bcs_net : 0,
        'collectivite_sante' => $salariesAntérieurs ? $salariesAntérieurs->collectivite_sante_net : 0,
        'collectivite_education' => $salariesAntérieurs ? $salariesAntérieurs->collectivite_education_net : 0,
        'personnels_saisonniers' => $salariesAntérieurs ? $salariesAntérieurs->personnels_saisonniers_net : 0,
        'epn' => $salariesAntérieurs ? $salariesAntérieurs->epn_net : 0,
    ];

        // Créer la demande de fonds avec tous les champs
        $validatedData['status'] = $request->input('status', 'en_attente');
         DemandeFonds::create([
            'user_id' => Auth::id(),
            'mois' => $request->mois,
            'annee' => $request->annee,
            'total_demande' => $request->total_demande,
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
            'poste_id' => $request->poste_id,
            'date_reception' => $request->date_reception,
        ]);       

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
            'mois' => 'required|string|max:20',
            'annee' => 'required|numeric',
            'total_demande' => 'required|numeric',
            'status' => 'required|string',
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
            'poste_id' => 'required|exists:postes,id',
            'date_reception' => 'required|date',
        ]);

        // Mettre à jour la demande de fonds
        $demandeFonds->update($request->all());

        return redirect()->route('demandes.index')->with('success', 'Demande de fonds mise à jour avec succès.');
    }

    public function destroy(DemandeFonds $demandeFonds)
    {
        // Supprimer la demande de fonds
        $demandeFonds->delete();
        return redirect()->route('demandes.index')->with('success', 'Demande de fonds supprimée avec succès.');
    }

    public function show(DemandeFonds $demandeFonds)
    {
        $demandes = DemandeFonds::with('user', 'poste')->get();
        return view('demandes.show', compact('demandes'));
    }
}

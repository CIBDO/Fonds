<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\DeclarationPcs;
use App\Models\CotisationTrie;
use App\Models\BureauDouane;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

/**
 * Génération de l'état des références (déclarations PCS + cotisations TRIE) pour un poste émetteur.
 */
class EtatReferencesController extends Controller
{
    /**
     * Générer l'état des références (déclarations et cotisations) pour le poste émetteur connecté.
     * Fait ressortir la référence / référence de paiement pour chaque ligne.
     */
    public function posteEmetteur(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->poste_id) {
            Alert::error('Erreur', 'Aucun poste assigné à votre compte');
            return redirect()->back();
        }

        $annee = (int) $request->get('annee', date('Y'));
        $programme = $request->get('programme'); // null = tous (UEMOA + AES)
        $poste = $user->poste;

        // --- Déclarations PCS ---
        $queryDeclarations = DeclarationPcs::with(['poste', 'bureauDouane'])
            ->where('annee', $annee);

        if ($programme) {
            $queryDeclarations->where('programme', $programme);
        }

        if ($poste->isRgd()) {
            $bureauxIds = BureauDouane::where('poste_rgd_id', $poste->id)
                ->where('actif', true)
                ->pluck('id');
            $queryDeclarations->where(function ($q) use ($poste, $bureauxIds) {
                $q->where('poste_id', $poste->id)
                    ->whereNull('bureau_douane_id')
                    ->orWhereIn('bureau_douane_id', $bureauxIds);
            });
        } else {
            $queryDeclarations->where('poste_id', $poste->id)
                ->whereNull('bureau_douane_id');
        }

        $declarations = $queryDeclarations->orderBy('programme')->orderBy('mois')->get();

        // --- Cotisations TRIE ---
        $cotisations = CotisationTrie::with(['bureauTrie'])
            ->where('poste_id', $poste->id)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->orderBy('mois')
            ->orderBy('bureau_trie_id')
            ->get();

        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $pdf = Pdf::loadView('pcs.pdf.etat-references-poste-emetteur', compact(
            'declarations',
            'cotisations',
            'poste',
            'annee',
            'programme',
            'moisList'
        ));

        $pdf->setPaper('A4', 'landscape');

        $nomFichier = "Etat_References_{$poste->nom}_{$annee}.pdf";
        return $pdf->download($nomFichier);
    }
}

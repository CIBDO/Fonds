<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des paiements FNL {{ $periodeLibelle }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 10px; font-size: 9px; line-height: 1.2; }
        .header { margin-bottom: 25px; }
        .header-left { float: left; width: 45%; text-align: left; }
        .header-right { float: right; width: 45%; text-align: right; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .title { font-weight: bold; font-size: 12px; margin: 3px 0; }
        .subtitle { font-size: 10px; margin: 2px 0; }
        .stars { font-size: 8px; margin: 3px 0; }
        .main-title { font-size: 13px; font-weight: bold; text-decoration: underline; margin: 25px 0 8px; text-align: center; }
        .montants { text-align: center; font-size: 10px; font-style: italic; margin-bottom: 15px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 8px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .total-row { background: #e8e8e8; font-weight: bold; }
        .signature { margin-right: 100px; text-align: right; margin-top: 30px; }
        .date-signature { font-size: 10px; margin-bottom: 50px; }
        .agent-signature { font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTÈRE DE L'ÉCONOMIE</div>
            <div class="title">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            {{-- <div class="subtitle">AGENCE COMPTABLE CENTRALE DES DEPÔTS</div> --}}
        </div>
        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <div class="main-title">
        ÉTAT DES PAIEMENTS FNL {{ $periodeLibelle }}
        @if($posteNom)
            — {{ $posteNom }}
        @endif
    </div>
    <div class="montants">(Montants en francs CFA)</div>

    <table>
        <thead>
            <tr>
                <th>Postes</th>
                @unless($nomMois)
                    <th>Période</th>
                @endunless
                <th>Retenue FNL</th>
                <th>Réf. paiement</th>
                @if($afficherStatut)
                    <th>Statut</th>
                @endif
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paiements as $paiement)
            <tr>
                <td class="text-left">{{ $paiement->poste->nom ?? '—' }}</td>
                @unless($nomMois)
                    <td>{{ $paiement->nom_mois }} {{ $paiement->annee }}</td>
                @endunless
                <td class="text-right">{{ number_format($paiement->retenue_fnl, 0, ',', ' ') }}</td>
                <td class="text-left">
                    {{ $paiement->reference_paiement ?: '—' }}
                    @if($paiement->date_paiement)
                        du {{ $paiement->date_paiement->format('d/m/Y') }}
                    @endif
                </td>
                @if($afficherStatut)
                    <td>
                        @if($paiement->statut === 'valide') Validé
                        @elseif($paiement->statut === 'rejete') Rejeté
                        @else Soumis
                        @endif
                    </td>
                @endif
                <td class="text-left">{{ $paiement->observation }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 4 + ($nomMois ? 0 : 1) + ($afficherStatut ? 1 : 0) }}">Aucun paiement pour cette période.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td class="text-left">TOTAL</td>
                @unless($nomMois)
                    <td></td>
                @endunless
                <td class="text-right">{{ number_format($totalRetenue, 0, ',', ' ') }}</td>
                <td></td>
                @if($afficherStatut)
                    <td></td>
                @endif
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div class="date-signature">
            Bamako, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            Le Responsable du Poste comptable
        </div>
    </div>
</body>
</html>

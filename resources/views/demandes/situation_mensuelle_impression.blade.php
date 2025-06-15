<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Fonds Salaires du Mois de {{ $mois }} {{ $annee }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-left {
            float: left;
            width: 45%;
            text-align: left;
        }

        .header-right {
            float: right;
            width: 45%;
            text-align: right;
        }

        .header-center {
            clear: both;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            font-weight: bold;
            font-size: 14px;
            margin: 5px 0;
        }

        .subtitle {
            font-size: 12px;
            margin: 2px 0;
        }

        .stars {
            font-size: 10px;
            margin: 5px 0;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .signature {
            margin-top: 40px;
            text-align: center;
        }

        .date-signature {
            font-size: 12px;
            margin-bottom: 60px;
        }

        .agent-signature {
            font-size: 12px;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Bouton d'impression -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; background-color: #007bff; color: white; border: none; border-radius: 5px;">
            Imprimer
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; background-color: #6c757d; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            Fermer
        </button>
    </div>

    <!-- En-tête -->
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTÈRE DE L'ÉCONOMIE</div>
            <div class="title">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">AGENCE COMPTABLE CENTRALE DU TRÉSOR</div>
        </div>

        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        DEMANDE DE FONDS SALAIRES DU MOIS DE {{ strtoupper($mois) }} {{ $annee }}
    </div>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>POSTES</th>
                <th>SALAIRE BRUT</th>
                <th>SALAIRE DEMANDÉ</th>
                <th>SALAIRE ENVOYÉ</th>
                <th>EXCÉDENT/DÉFICIT</th>
                <th>OBSERVATIONS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demandesParPoste as $demande)
            <tr>
                <td class="text-left"><strong>{{ $demande['poste'] }}</strong></td>
                <td class="text-right">{{ number_format($demande['salaire_brut'], 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($demande['salaire_demande'], 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($demande['salaire_envoye'], 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($demande['excedent_deficite'], 0, ',', ' ') }}</td>
                <td>-</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic;">
                    Aucune donnée disponible pour {{ $mois }} {{ $annee }}
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($demandesParPoste->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td class="text-left"><strong>TOTAL GÉNÉRAL</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['salaire_brut'], 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['salaire_demande'], 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['salaire_envoye'], 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['excedent_deficite'], 0, ',', ' ') }}</strong></td>
                <td>-</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Signature -->
    <div class="signature">
        <div class="date-signature">
            Bamako, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            L'Agent Comptable Central
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.addEventListener('load', function() {
        //     window.print();
        // });
    </script>
</body>
</html>

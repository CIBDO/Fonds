<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etat Consolidé - Demandes de Fonds</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header {
            margin-bottom: 25px;
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

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            font-weight: bold;
            font-size: 13px;
            margin: 3px 0;
        }

        .subtitle {
            font-size: 11px;
            margin: 2px 0;
        }

        .stars {
            font-size: 9px;
            margin: 3px 0;
        }

        .main-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 25px 0 20px 0;
            text-align: center;
        }

        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .filters h3 {
            font-size: 11px;
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .filters p {
            margin: 3px 0;
            font-size: 9px;
        }

        .totaux-cards {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .totaux-card {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .totaux-card h4 {
            font-size: 10px;
            margin: 0 0 5px 0;
            color: #555;
        }

        .totaux-card p {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table thead {
            background-color: #2c3e50;
            color: white;
        }

        table th {
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 8px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tfoot {
            background-color: #e9ecef;
            font-weight: bold;
        }

        table tfoot td {
            padding: 6px 4px;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .footer {
            position: fixed;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .signature {
            margin-right: 100px;
            text-align: right;
        }

        .date-signature {
            font-size: 11px;
            margin-bottom: 50px;
        }

        .agent-signature {
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
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
        ÉTAT CONSOLIDÉ - DEMANDES DE FONDS DE SALAIRE
    </div>

    <!-- Filtres appliqués -->
    @if(!empty($filtres) && (isset($filtres['poste']) || isset($filtres['mois']) || isset($filtres['annee']) || isset($filtres['status']) || isset($filtres['date_debut']) || isset($filtres['date_fin'])))
    <div class="filters">
        {{-- <h3>Filtres appliqués :</h3> --}}
        @if(isset($filtres['poste']) && $filtres['poste'])
            <p><strong>Poste :</strong> {{ $filtres['poste'] }}</p>
        @endif
        @if(isset($filtres['mois']) && $filtres['mois'])
            <p><strong>Mois :</strong> {{ $filtres['mois'] }}</p>
        @endif
        @if(isset($filtres['annee']) && $filtres['annee'])
            <p><strong>Année :</strong> {{ $filtres['annee'] }}</p>
        @endif
        @if(isset($filtres['status']) && $filtres['status'])
            <p><strong>Statut :</strong>
                @if($filtres['status'] == 'en_attente')
                    En attente
                @elseif($filtres['status'] == 'approuve')
                    Approuvé
                @elseif($filtres['status'] == 'rejete')
                    Rejeté
                @endif
            </p>
        @endif
        @if((isset($filtres['date_debut']) && $filtres['date_debut']) || (isset($filtres['date_fin']) && $filtres['date_fin']))
            <p><strong>Période :</strong>
                @if(isset($filtres['date_debut']) && $filtres['date_debut'])
                    Du {{ \Carbon\Carbon::parse($filtres['date_debut'])->format('d/m/Y') }}
                @endif
                @if(isset($filtres['date_fin']) && $filtres['date_fin'])
                    Au {{ \Carbon\Carbon::parse($filtres['date_fin'])->format('d/m/Y') }}
                @endif
            </p>
        @endif
    </div>
    @endif

    <!-- Cartes de totaux -->
    <div class="totaux-cards">
        <div class="totaux-card">
            <h4>Total Salaires Bruts</h4>
            <p>{{ number_format($totaux['total_courant'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="totaux-card">
            <h4>Total Recettes Douanières</h4>
            <p>{{ number_format($totaux['montant_disponible'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="totaux-card">
            <h4>Total Soldes</h4>
            <p>{{ number_format($totaux['solde'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="totaux-card">
            <h4>Total Montants Envoyés</h4>
            <p>{{ number_format($totaux['montant_envoye'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    <!-- Tableau des demandes -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Poste</th>
                <th style="width: 15%;">Mois/Année</th>
                <th style="width: 15%;" class="text-right">Total Courant</th>
                <th style="width: 15%;" class="text-right">Montant Dispo.</th>
                <th style="width: 15%;" class="text-right">Solde</th>
                <th style="width: 15%;" class="text-right">Montant Envoyé</th>
                <th style="width: 15%;">Date Envoi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demandes as $demande)
                <tr>
                    <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                    <td class="text-right">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                    <td class="text-right">
                        @if($demande->status === 'approuve' && $demande->montant)
                            {{ number_format($demande->montant, 0, ',', ' ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($demande->date_envois)
                            {{ \Carbon\Carbon::parse($demande->date_envois)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Aucune demande de fonds trouvée.</td>
                </tr>
            @endforelse
        </tbody>
        @if($demandes->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong>TOTAUX :</strong></td>
                    <td class="text-right"><strong>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totaux['montant_disponible'], 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totaux['solde'], 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totaux['montant_envoye'], 0, ',', ' ') }}</strong></td>
                    <td></td>
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
            L'Agent Comptable Central du Trésor
        </div>
    </div>

    <!-- Pied de page -->
    {{-- <div class="footer">
        <p>Document confidentiel - Direction Générale du Trésor et de la Comptabilité Publique</p>
        <p>Page générée le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div> --}}
</body>
</html>


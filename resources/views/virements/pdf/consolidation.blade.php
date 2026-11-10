<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Consolidation VIR par banque</title>
    @include('virements.pdf._styles', ['orientation' => 'landscape'])
</head>
<body>
    @include('virements.pdf._header')

    <div class="main-title">
        Consolidation des virements salariaux par banque
    </div>

    <div class="meta">
        @if(!empty($filtreLibelle))
            {{ $filtreLibelle }}
        @else
            Ensemble des VIR soumis
        @endif
    </div>

    <div class="note-montants">(Montants en francs CFA)</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:5%;">N°</th>
                <th style="width:35%;">Banque</th>
                <th style="width:12%;">Nb VIR</th>
                <th style="width:16%;">Total</th>
                <th style="width:16%;">Payé</th>
                <th style="width:16%;">Non payé</th>
            </tr>
        </thead>
        <tbody>
            @php
                $tNb = 0; $tTot = 0; $tPaye = 0; $tNon = 0;
            @endphp
            @forelse($parBanque as $index => $row)
                @php
                    $tNb += $row['nb'];
                    $tTot += $row['total'];
                    $tPaye += $row['paye'];
                    $tNon += $row['non_paye'];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row['banque'] }}</td>
                    <td>{{ $row['nb'] }}</td>
                    <td class="text-right">{{ number_format($row['total'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($row['paye'], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($row['non_paye'], 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucune donnée</td>
                </tr>
            @endforelse
            @if(collect($parBanque)->isNotEmpty())
                <tr class="total-row">
                    <td colspan="2" class="text-right">TOTAL</td>
                    <td>{{ $tNb }}</td>
                    <td class="text-right">{{ number_format($tTot, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($tPaye, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($tNon, 0, ',', ' ') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature">
        <div class="date-signature">
            Bamako, le {{ now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            L'Agent Comptable Central du Trésor
        </div>
    </div>
</body>
</html>

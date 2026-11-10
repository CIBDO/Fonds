<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des virements salariaux</title>
    @include('virements.pdf._styles', ['orientation' => 'landscape'])
</head>
<body>
    @include('virements.pdf._header')

    <div class="main-title">
        Liste des virements salariaux soumis
    </div>

    <div class="meta">
        @if(!empty($filtreLibelle))
            {{ $filtreLibelle }}
        @else
            Ensemble des VIR soumis
        @endif
        · {{ $virements->count() }} VIR
    </div>

    <div class="note-montants">(Montants en francs CFA)</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:4%;">N°</th>
                <th style="width:14%;">N° VIR</th>
                <th style="width:18%;">Poste</th>
                <th style="width:22%;">Banque</th>
                <th style="width:12%;">Période</th>
                <th style="width:14%;">Montant</th>
                <th style="width:10%;">Statut</th>
                <th style="width:6%;">Lignes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($virements as $index => $virement)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $virement->numero_vir }}</td>
                    <td class="text-left">{{ $virement->poste->libelleAffiche() ?? ($virement->poste->nom ?? '—') }}</td>
                    <td class="text-left">{{ $virement->banque->nom ?? '—' }}</td>
                    <td>{{ $virement->mois }} {{ $virement->annee }}</td>
                    <td class="text-right">{{ number_format((float) $virement->montant, 0, ',', ' ') }}</td>
                    <td>{{ $virement->isPaye() ? 'Payé' : 'Non payé' }}</td>
                    <td>{{ $virement->lignes_count ?? $virement->lignes->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucun virement</td>
                </tr>
            @endforelse
            @if($virements->isNotEmpty())
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format((float) $virements->sum('montant'), 0, ',', ' ') }}</td>
                    <td></td>
                    <td></td>
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

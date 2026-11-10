<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>VIR {{ $virement->numero_vir }}</title>
    @include('virements.pdf._styles', ['orientation' => 'landscape'])
</head>
<body>
    @include('virements.pdf._header')

    <div class="main-title">
        Fiche de virement salarial — N° {{ $virement->numero_vir }}
    </div>

    @if(!empty($regionLibelle))
        <div class="region">{{ $regionLibelle }}</div>
    @endif

    <div class="meta">
        Période : {{ $virement->mois }} {{ $virement->annee }}
        · Poste : {{ $posteNom ?? ($virement->poste->libelleAffiche() ?? '-') }}
    </div>

    <div class="note-montants">(Montants en francs CFA)</div>

    <table class="info-box">
        <tr>
            <td class="label">N° VIR</td>
            <td>{{ $virement->numero_vir }}</td>
            <td class="label">Banque</td>
            <td>{{ $virement->banque->nom ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Montant VIR</td>
            <td>{{ number_format((float) $virement->montant, 0, ',', ' ') }}</td>
            <td class="label">Statut</td>
            <td>{{ $virement->isPaye() ? 'Payé' : 'Non payé' }}</td>
        </tr>
        <tr>
            <td class="label">Date paiement</td>
            <td>{{ $virement->isPaye() ? optional($virement->date_paiement)->format('d/m/Y H:i') : '—' }}</td>
            <td class="label">Observation</td>
            <td>{{ $virement->observation ?: '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Lignes détail</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:5%;">N°</th>
                <th style="width:10%;">N° BE</th>
                <th style="width:10%;">N° Mdt</th>
                <th style="width:8%;">Ord.</th>
                <th style="width:10%;">Programme</th>
                <th style="width:28%;">Créancier</th>
                <th style="width:14%;">N° Compte</th>
                <th style="width:15%;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($virement->lignes as $index => $ligne)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $ligne->num_be }}</td>
                    <td>{{ $ligne->num_mdt }}</td>
                    <td>{{ $ligne->ord }}</td>
                    <td>{{ $ligne->programme }}</td>
                    <td class="text-left">{{ $ligne->creancier_libelle }}</td>
                    <td>{{ $ligne->num_compte }}</td>
                    <td class="text-right">{{ number_format((float) $ligne->montant, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucune ligne détail</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="7" class="text-right">TOTAL LIGNES</td>
                <td class="text-right">{{ number_format((float) $virement->lignes->sum('montant'), 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div class="date-signature">
            {{ $posteNom ?? '' }}, le {{ now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            {{ $signatureLibelle ?? 'Le Responsable du Poste comptable' }}
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bordereau VIR {{ $bordereau->mois }} {{ $bordereau->annee }}</title>
    @include('virements.pdf._styles', ['orientation' => 'landscape'])
</head>
<body>
    @include('virements.pdf._header')

    <div class="main-title">
        Bordereau de propositions de virements salariaux
    </div>

    @if(!empty($regionLibelle))
        <div class="region">{{ $regionLibelle }}</div>
    @endif

    <div class="meta">
        Période : {{ $bordereau->mois }} {{ $bordereau->annee }}
        · Poste : {{ $posteNom ?? ($bordereau->poste->libelleAffiche() ?? '-') }}
        · Statut : {{ $bordereau->status === 'soumis' ? 'Soumis' : 'Brouillon' }}
        @if($bordereau->soumis_at)
            · Soumis le {{ optional($bordereau->soumis_at)->format('d/m/Y H:i') }}
        @endif
    </div>

    <div class="note-montants">(Montants en francs CFA)</div>

    <table class="info-box">
        <tr>
            <td class="label">Couverture demande</td>
            <td>{{ number_format((float) ($bordereau->demandeFonds->montant ?? 0), 0, ',', ' ') }}</td>
            <td class="label">Total VIR</td>
            <td>{{ number_format((float) $bordereau->virements->sum('montant'), 0, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Nombre de VIR</td>
            <td>{{ $bordereau->virements->count() }}</td>
            <td class="label">Période couverture</td>
            <td>
                @if($bordereau->periode_debut || $bordereau->periode_fin)
                    {{ optional($bordereau->periode_debut)->format('d/m/Y') ?: '…' }}
                    —
                    {{ optional($bordereau->periode_fin)->format('d/m/Y') ?: '…' }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Virements du bordereau</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width:5%;">N°</th>
                <th style="width:16%;">N° VIR</th>
                <th style="width:28%;">Banque</th>
                <th style="width:12%;">Période</th>
                <th style="width:16%;">Montant</th>
                <th style="width:12%;">Statut</th>
                <th style="width:11%;">Nb lignes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bordereau->virements as $index => $virement)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $virement->numero_vir }}</td>
                    <td class="text-left">{{ $virement->banque->nom ?? '—' }}</td>
                    <td>{{ $virement->mois }} {{ $virement->annee }}</td>
                    <td class="text-right">{{ number_format((float) $virement->montant, 0, ',', ' ') }}</td>
                    <td>{{ $virement->isPaye() ? 'Payé' : 'Non payé' }}</td>
                    <td>{{ $virement->lignes->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun VIR sur ce bordereau</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format((float) $bordereau->virements->sum('montant'), 0, ',', ' ') }}</td>
                <td></td>
                <td>{{ $bordereau->virements->sum(fn ($v) => $v->lignes->count()) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div class="date-signature">
            {{ $posteNom ?? '' }}, le {{ now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            Le Responsable du Poste comptable
        </div>
    </div>
</body>
</html>

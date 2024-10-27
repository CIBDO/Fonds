@extends('layouts.master')
@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <h3 class="page-title">Bienvenue</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @php
            $cards = [
                ['title' => 'Montant Demandé', 'value' => $fondsDemandes, 'icon' => 'dash-icon-04.svg'],
                ['title' => 'Recettes Douanières', 'value' => $fondsRecettes, 'icon' => 'dash-icon-02.svg'],
                ['title' => 'Fonds à Envoyer', 'value' => $fondsEnCours, 'icon' => 'dash-icon-03.svg'],
                ['title' => 'Fonds Envoyés', 'value' => $paiementsEffectues, 'icon' => 'dash-icon-04.svg'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6 style="font-size: 18px; color: hsl(210, 79%, 45%); font-weight: bold;">{{ $card['title'] }}</h6>
                            <h3>{{ number_format($card['value'], 0, '', ' ') }}</h3>
                        </div>
                        <div class="db-icon">
                            <img src="{{ asset('assets/img/icons/' . $card['icon']) }}" alt="Dashboard Icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mb-4">
        <h3 style="font-size: 20px; color: hsl(240, 26%, 92%); font-weight: bold; text-align: center; background-color: #574ae6; padding: 10px; border-radius: 20px;">Situation Financière</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Total Net</th>
                    <th>Total Revers</th>
                    <th>Total Courant</th>
                    <th>Total Ancien</th>
                    <th>Total Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesFonds as $demande)
                    <tr>
                        <td>{{ $demande->poste->nom }}</td>
                        <td>{{ number_format($demande->total_net, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_revers, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_courant, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_ancien, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_courant - $demande->total_ancien, 0, '', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

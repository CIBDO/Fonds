@extends('layouts.master')

@section('title', 'Bureaux TRIE - CCIM')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('trie.cotisations.index') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-coins me-1"></i>Cotisations
    </a>
</div>

@if(in_array(Auth::user()->role, ['admin', 'acct']))
<x-vuexy.alert type="info">
    <strong>Vue Administrateur :</strong> Vous pouvez voir et gérer les bureaux de tous les postes.
</x-vuexy.alert>
@else
<x-vuexy.alert type="success">
    <strong>Mon Poste :</strong> Vous gérez les bureaux de votre poste uniquement.
</x-vuexy.alert>
@endif

@if($postes && $postes->count() > 0)
<div class="row g-6">
    @foreach($postes as $poste)
        @php
            $bureauxPoste = $poste->bureauxTrie;
        @endphp

        <div class="col-12">
            <x-vuexy.card :title="$poste->nom" icon="tabler-map-pin">
                <x-slot:header>
                    <a href="{{ route('trie.bureaux.manage', $poste->id) }}" class="btn btn-sm btn-primary">
                        <i class="ti tabler-settings me-1"></i>Gérer les Bureaux
                    </a>
                </x-slot:header>

                @if($bureauxPoste->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Code Bureau</th>
                                    <th>Nom du Bureau</th>
                                    <th>Description</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bureauxPoste as $bureau)
                                <tr>
                                    <td><strong class="text-primary">{{ $bureau->code_bureau }}</strong></td>
                                    <td>{{ $bureau->nom_bureau }}</td>
                                    <td><small class="text-muted">{{ $bureau->description ?? '-' }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $bureau->actif ? 'success' : 'secondary' }}">
                                            {{ $bureau->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-vuexy.alert type="warning">
                        <p class="mb-2"><strong>Aucun bureau enregistré pour ce poste.</strong></p>
                        <p class="text-muted mb-3">Vous devez créer au moins un bureau pour pouvoir saisir des cotisations TRIE.</p>
                        <a href="{{ route('trie.bureaux.manage', $poste->id) }}" class="btn btn-primary">
                            <i class="ti tabler-plus me-1"></i>Créer le premier bureau
                        </a>
                    </x-vuexy.alert>
                @endif
            </x-vuexy.card>
        </div>
    @endforeach
</div>
@else
<x-vuexy.alert type="danger">
    <h4 class="alert-heading">Aucun poste associé</h4>
    <p class="mb-0">Vous n'êtes pas associé à un poste. Veuillez contacter l'administrateur.</p>
</x-vuexy.alert>
@endif
@endsection

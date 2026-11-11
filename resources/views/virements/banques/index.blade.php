@extends('layouts.master')

@section('title', 'Banques')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('banques.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouvelle banque
    </a>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('banques.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Recherche</label>
            <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Code ou nom">
        </div>
        <div class="col-md-3">
            <label class="form-label">Statut</label>
            <select name="actif" class="form-select">
                <option value="">Tous</option>
                <option value="1" @selected(request('actif') === '1')>Actives</option>
                <option value="0" @selected(request('actif') === '0')>Inactives</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Référentiel des banques" icon="tabler-building-bank">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ $banques->total() }} banque(s)</span>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="12%">Code</th>
                    <th>Nom</th>
                    <th width="12%" class="text-center">Statut</th>
                    <th width="16%" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banques as $banque)
                <tr>
                    <td><span class="badge bg-label-primary">{{ $banque->code }}</span></td>
                    <td class="fw-medium">{{ $banque->nom }}</td>
                    <td class="text-center">
                        @if($banque->actif)
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('banques.edit', $banque) }}"
                           class="btn btn-icon btn-sm btn-text-primary rounded-pill"
                           title="Modifier">
                            <i class="icon-base ti tabler-edit icon-22px"></i>
                        </a>
                        <form action="{{ route('banques.toggle-actif', $banque) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit"
                                    class="btn btn-icon btn-sm btn-text-{{ $banque->actif ? 'warning' : 'success' }} rounded-pill"
                                    title="{{ $banque->actif ? 'Désactiver' : 'Activer' }}">
                                <i class="icon-base ti tabler-power icon-22px"></i>
                            </button>
                        </form>
                        <form action="{{ route('banques.destroy', $banque) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer la banque {{ $banque->code }} ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                                    title="Supprimer">
                                <i class="icon-base ti tabler-trash icon-22px"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-body-secondary py-4">
                        Aucune banque enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $banques->links('custom.pagination') }}
    </div>
</x-vuexy.card>
@endsection

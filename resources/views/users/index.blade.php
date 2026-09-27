@extends('layouts.master')

@section('title', 'Gestion des Utilisateurs')

@include('partials.vuexy.datatables-assets')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <a href="{{ route('users.edit', auth()->user()->id) }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-user me-1"></i>Mon Profil
    </a>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouvel Utilisateur
    </a>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('users.index') }}" id="filterForm" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-bold">Nom & Prénom</label>
            <input type="text" class="form-control" name="name" placeholder="Rechercher par nom..." value="{{ request('name') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Email</label>
            <input type="text" class="form-control" name="email" placeholder="Rechercher par email..." value="{{ request('email') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">Rôle</label>
            <select class="form-select" name="role">
                <option value="">Tous les rôles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="tresorier" {{ request('role') == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                <option value="acct" {{ request('role') == 'acct' ? 'selected' : '' }}>ACCT</option>
                <option value="accd" {{ request('role') == 'accd' ? 'selected' : '' }}>ACCD</option>
                <option value="superviseur" {{ request('role') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">Statut</label>
            <select class="form-select" name="status">
                <option value="">Tous</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-label-secondary" title="Réinitialiser">
                <i class="icon-base ti tabler-x"></i>
            </a>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Utilisateurs" icon="tabler-users">
    <x-slot:header>
        <span class="badge bg-label-secondary">{{ $users->total() }} utilisateur(s)</span>
    </x-slot:header>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="usersDataTable">
            <thead class="table-light">
                <tr>
                    <th width="5%">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Utilisateur</th>
                    <th>Contact</th>
                    <th>Rôle</th>
                    <th>Poste</th>
                    <th>Statut</th>
                    <th class="text-center" width="80">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @if (auth()->user()->role === 'admin' || auth()->user()->id === $user->id)
                    <tr data-user-id="{{ $user->id }}">
                        <td>
                            <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-body-secondary">Inscrit le {{ $user->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            @if($user->phone)
                                <small class="text-body-secondary">{{ $user->phone }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $rolesLibelles = [
                                    'admin' => 'Administrateur',
                                    'tresorier' => 'Trésorier',
                                    'acct' => 'ACCT',
                                    'accd' => 'ACCD',
                                    'superviseur' => 'Superviseur',
                                    'direction' => 'Direction',
                                ];
                            @endphp
                            <span class="badge {{ $user->role === 'admin' ? 'bg-label-warning' : 'bg-label-primary' }}">
                                {{ $rolesLibelles[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary">{{ $user->poste->nom ?? 'Non défini' }}</span>
                        </td>
                        <td>
                            @if($user->isActive())
                                <span class="badge bg-label-success">Actif</span>
                            @else
                                <span class="badge bg-label-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (auth()->user()->id === $user->id || auth()->user()->role === 'admin')
                                <a href="{{ route('users.edit', $user->id) }}"
                                   class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                   data-bs-toggle="tooltip"
                                   title="Modifier">
                                    <i class="icon-base ti tabler-edit icon-22px"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
        <div class="text-body-secondary small">
            Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateurs
        </div>
        {{ $users->links('custom.pagination') }}
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#usersDataTable').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        ordering: true,
        order: [[1, 'asc']],
        language: typeof datatablesFrench !== 'undefined' ? datatablesFrench : window.DGTCP_DATATABLES_FR,
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 6] }
        ]
    });

    $('#selectAll').on('click', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush

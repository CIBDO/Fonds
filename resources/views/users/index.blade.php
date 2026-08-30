@extends('layouts.master')

@section('title', 'Gestion des Utilisateurs')

@include('partials.vuexy.datatables-assets')

@section('content')
<x-vuexy.page-header title="Gestion des Utilisateurs" subtitle="Administration des comptes utilisateurs">
    <x-slot:actions>
        <a href="{{ route('users.edit', auth()->user()->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="icon-base ti tabler-user me-1"></i>Mon Profil
        </a>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-plus me-1"></i>Nouvel Utilisateur
        </a>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Liste des Utilisateurs" icon="tabler-users">
    <form method="GET" action="{{ route('users.index') }}" id="filterForm" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Nom & Prénom</label>
                <input type="text" class="form-control form-control-sm" name="name" placeholder="Rechercher par nom..." value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control form-control-sm" name="email" placeholder="Rechercher par email..." value="{{ request('email') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Rôle</label>
                <select class="form-select form-select-sm" name="role">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="tresorier" {{ request('role') == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Statut</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="icon-base ti tabler-search"></i> Filtrer
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="icon-base ti tabler-x"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="usersDataTable">
            <thead>
                <tr>
                    <th width="5%">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                    </th>
                    <th>Utilisateur</th>
                    <th>Contact</th>
                    <th>Rôle</th>
                    <th>Poste</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
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
                            @if($user->role === 'admin')
                                <span class="badge bg-label-warning">Administrateur</span>
                            @else
                                <span class="badge bg-label-primary">Employé</span>
                            @endif
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
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Modifier">
                                    <i class="icon-base ti tabler-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
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
        searching: true,
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

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
});
</script>
@endpush

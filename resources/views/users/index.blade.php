@extends('layouts.master')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <h3 class="page-title">Utilisateurs</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                            <li class="breadcrumb-item active">Tous les Utilisateurs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="search-user-form">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Prénoms & Nom" value="{{ request('name') }}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" name="email" placeholder=" Email" value="{{ request('email') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="search-user-btn">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table comman-shadow">
                    <div class="card-body">

                        <div class="page-header">
                        <div class="row align-items-center">
                        <div class="col">
                        <h3 class="page-title">USERS</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                        <a href="#" class="btn btn-outline-gray me-2 active"><i class="feather-list"></i></a>
                        <a href="#" class="btn btn-outline-gray me-2"><i class="feather-grid"></i></a>
                        <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i> Download</a>
                        <a href="{{route('users.create')}}" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                        </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table border-0 table-hover table-center mb-0" id="userTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr data-user-id="{{ $user->id }}">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>{{ $user->isActive() ? 'Actif' : 'Inactif' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                                <i class="feather-edit"></i> Éditer
                                            </a>
                                            @if($user->isActive())
                                            <form action="{{ route('users.deactivate', $user->id) }} " method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit">Désactiver</button>
                                            </form>
                                                 @else
                                            <form action="{{ route('users.activate', $user->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit">Activer</button>
                                            </form>
                                                 @endif
                                            {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                    <i class="feather-trash"></i> Supprimer
                                                </button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr id="no-results" style="display: none;">
                                        <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Ajoutez cette ligne pour la pagination --}}
                        <div class="d-flex justify-content-center">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Filtrer les utilisateurs
        $('#searchButton').click(function() {
            var searchId = $('#searchId').val().toLowerCase();
            var searchName = $('#searchName').val().toLowerCase();
            var found = false;

            $('#userTable tbody tr').each(function() {
                var id = $(this).find('td').eq(0).text().toLowerCase();
                var name = $(this).find('td').eq(1).text().toLowerCase();

                if ((id.includes(searchId) || searchId === '') && (name.includes(searchName) || searchName === '')) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

            $('#no-results').toggle(!found);
        });

        // Désactiver l'utilisateur
        $('#userTable').on('click', '.deactivate-button', function() {
            var userId = $(this).closest('tr').data('user-id');
            if (confirm("Êtes-vous sûr de vouloir désactiver cet utilisateur ?")) {
                $.post(`/api/users/${userId}/deactivate`, {_token: '{{ csrf_token() }}'})
                    .done(function() {
                        // Mettre à jour l'affichage
                        $('tr[data-user-id="' + userId + '"]').find('.badge').removeClass('badge-success').addClass('badge-danger').text('Désactivé');
                        $('tr[data-user-id="' + userId + '"]').find('.deactivate-button').remove();  // Enlève le bouton "Désactiver"
                        $('tr[data-user-id="' + userId + '"]').find('.activate-button').show();  // Affiche le bouton "Activer"
                    })
                    .fail(function() {
                        alert("Erreur lors de la désactivation de l'utilisateur.");
                    });
            }
        });

        // Activer l'utilisateur (si nécessaire)
        $('#userTable').on('click', '.activate-button', function() {
            var userId = $(this).closest('tr').data('user-id');
            // Ajoutez la logique pour activer l'utilisateur ici
        });
    });
    methods: {
    deactivateUser(userId) {
        axios.post(`/api/users/${userId}/deactivate`).then(response => {
            // Mettez à jour le statut de l'utilisateur
            const user = this.users.find(u => u.id === userId);
            if (user) {
                user.active = false;  // Désactive l'utilisateur localement
            }
            alert(response.data.success);
        }).catch(error => {
            console.error("Erreur lors de la désactivation de l'utilisateur:", error);
        });
    },
    activateUser(userId) {
        axios.post(`/api/users/${userId}/activate`).then(response => {
            // Mettez à jour le statut de l'utilisateur
            const user = this.users.find(u => u.id === userId);
            if (user) {
                user.active = true;  // Active l'utilisateur localement
            }
            alert(response.data.success);
        }).catch(error => {
            console.error("Erreur lors de l'activation de l'utilisateur:", error);
        });
    }
}

</script>
@endsection

@endsection

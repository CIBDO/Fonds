@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="text-center">Liste des Postes</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPosteModal">
                Ajouter un Poste
            </button>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Libellé</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($postes as $poste)
                        <tr>
                            <td>{{ $poste->id }}</td>
                            <td>{{ $poste->nom }}</td>
                            <td>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPosteModal{{ $poste->id }}">
                                    Éditer
                                </button>
                                <form action="{{ route('postes.destroy', $poste) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce poste ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modale pour éditer un poste -->
                        <div class="modal fade" id="editPosteModal{{ $poste->id }}" tabindex="-1" aria-labelledby="editPosteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPosteModalLabel">Modifier le Poste</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('postes.update', $poste) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="nom" class="form-label">Libellé</label>
                                                <input type="text" name="nom" class="form-control" value="{{ $poste->nom }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modale pour ajouter un nouveau poste -->
<div class="modal fade" id="addPosteModal" tabindex="-1" aria-labelledby="addPosteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPosteModalLabel">Ajouter un Poste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('postes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nom" class="form-label">Libellé</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

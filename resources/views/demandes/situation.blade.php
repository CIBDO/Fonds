@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes</li>
            </ul>
        </div>
    </div>
</div>

<form action="{{ route('demandes-fonds.index') }}" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="{{ request('mois') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <input type="text" name="total_courant" class="form-control" placeholder="Rechercher par montant ..." value="{{ request('total_courant') }}">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="search-student-btn">
                    <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Demandes</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-outline-gray me-2 active"><i class="feather-list"></i></a>
                            <a href="#" class="btn btn-outline-gray me-2"><i class="feather-grid"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i> Télécharger</a>
                            <a href="{{ route('demandes-fonds.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                <th>Mois</th>
                                <th>Date de Réception</th>
                                <th>Poste</th>
                                <th>Montant demandé</th>
                                <th>Date de la demande</th>
                                <th>Statut</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="demandes-table-body">
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ $demande->mois }}</td>
                                <td>{{ $demande->date_reception }}</td>
                                <td>{{ $demande->poste->nom }}</td>
                                <td>{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                                <td>{{ $demande->created_at }}</td>
                                <td>
                                    @if($demande->status === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($demande->status === 'approuve')
                                        <span class="badge bg-success">Approuvé</span>
                                    @elseif($demande->status === 'rejete')
                                        <span class="badge bg-danger">Rejeté</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $demande->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-sm btn-success me-2">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-sm btn-warning me-2">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}" class="btn btn-sm btn-info">
                                            <i class="feather-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            {{ $demandeFonds->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

@section('add-js')
    <!-- Inclure les fichiers DataTables CSS et JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#demandes-table').DataTable({
                order: [[1, 'desc']],  // Classe par date en ordre décroissant
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "/js/i18n/fr-FR.json",  // Chemin local vers le fichier de traduction
                    info: "",  // Désactiver le texte "showing x to y of z entries"
                    infoEmpty: "",  // Désactiver le texte quand il n'y a pas d'entrées
                    infoFiltered: ""  // Désactiver le texte de filtrage
                },
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                lengthChange: true,
                pageLength: 10,
                footerCallback: function ( row, data, start, end, display ) {
                    // Custom footer logic here
                }
            });
        });
    </script>
@stop
@endsection
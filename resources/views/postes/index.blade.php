@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Postes</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">Postes</li>
            </ul>
        </div>
    </div>
</div>

<div class="demande-group-form">
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                <input type="text" class="form-control" name="nom" placeholder="Rechercher par nom ...">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="search-student-btn">
                <button type="btn" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Postes</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-outline-gray me-2 active"><i
                                    class="feather-list"></i></a>
                            <a href="#" class="btn btn-outline-gray me-2"><i
                                    class="feather-grid"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i>
                                Download</a>
                            <a href= class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPosteModal"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                {{-- <th>
                                    <div class="form-check check-tables">
                                        <input class="form-check-input" type="checkbox" value="something">
                                    </div>
                                </th> --}}
                                <th>#</th>
                                <th>Nom du poste</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($postes as $poste)
                            <tr>
                                <td>{{ $poste->id }}</td>
                                <td>{{ $poste->nom }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{route('postes.show', $poste->id)}}" class="btn btn-sm bg-success-light me-2">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm bg-danger-light" data-bs-toggle="modal" data-bs-target="#editPosteModal{{ $poste->id }}">
                                                    <i class="feather-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="pagination-wrapper">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Ajout de la classe Bootstrap pour la pagination -->
                {{ $postes->links('pagination::bootstrap-4') }}
            </ul>
        </nav>
    </div>
</div>
</div>
@include('postes.add')
@include('postes.edit')
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
            $('#demandes-table').DataTable({
                order: [[1, 'desc']],  // Classe par date en ordre décroissant
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"  // Traduction en français
                },
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
                pageLength: 10
            });
        });
    </script>
@stop
@endsection

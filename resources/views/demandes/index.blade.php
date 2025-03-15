@extends('layouts.master')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="page-title">Demandes de Fonds</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Demandes</li>
        </ul>
    </div>
    <a href="{{ route('demandes-fonds.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Demande</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('demandes-fonds.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois..." value="{{ request('mois') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="total_courant" class="form-control" placeholder="Rechercher par montant..." value="{{ request('total_courant') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="demandes-table" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Date de Réception</th>
                        <th>Poste</th>
                        <th>Montant demandé (F CFA)</th>
                        <th>Date de la demande</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandeFonds as $demande)
                    <tr>
                        <td>{{ $demande->mois }}</td>
                        <td>{{ date('d/m/Y', strtotime($demande->date_reception)) }}</td>
                        <td>{{ $demande->poste->nom }}</td>
                        <td>{{ number_format($demande->solde, 0, ',', ' ') }} F CFA</td>
                        <td>{{ date('d/m/Y', strtotime($demande->created_at)) }}</td>
                        <td>
                            <span class="badge bg-{{ $demande->status === 'approuve' ? 'success' : ($demande->status === 'rejete' ? 'danger' : 'warning') }}">
                                {{ ucfirst($demande->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-sm btn-success me-2"><i class="feather-eye"></i></a>
                            <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-sm btn-warning me-2"><i class="feather-edit"></i></a>
                            <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}" class="btn btn-sm btn-info"><i class="feather-printer"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('add-js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
    $(document).ready(function() {
        $('#demandes-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/French.json"
            },
            responsive: true,
            pageLength: 10
        });
    });
</script>
@stop
@endsection

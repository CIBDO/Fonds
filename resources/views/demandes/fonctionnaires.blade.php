@extends('layouts.master')
@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes de Fonds</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes de Fonds</li>
            </ul>
        </div>
    </div>
</div>

<div class="card shadow-sm p-4 mb-4">
    <form action="{{ route('demandes-fonds.fonctionnaires') }}" method="GET">
        <div class="row g-3">
            <div class="col-lg-3">
                <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
            </div>
            <div class="col-lg-3">
                <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="{{ request('mois') }}">
            </div>
            <div class="col-lg-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Rechercher</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="demandes-table" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Poste</th>
                        <th>BCS</th>
                        <th>Santé</th>
                        <th>Éducation</th>
                        <th>Saisonnier</th>
                        <th>EPN</th>
                        <th>CED</th>
                        <th>ECOM</th>
                        <th>CFP-CPAM</th>
                        <th>Mois</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandeFonds as $demande)
                    <tr>
                        <td>{{ $demande->poste->nom }}</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->saisonnier_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('add-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        $('#demandes-table').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            language: { url: "/js/i18n/fr-FR.json" },
            responsive: true,
            paging: true,
            ordering: true,
            lengthChange: false,
            pageLength: 10,
        });
    });
</script>
@endsection
@endsection

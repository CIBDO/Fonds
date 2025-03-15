@extends('layouts.master')

@section('content')
{{-- <div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="page-title">Totaux par Mois</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Totaux par Mois</li>
        </ul>
    </div>
</div> --}}

<!-- Formulaire de sélection de l'année -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('demandes-fonds.totaux-par-mois') }}" method="GET" class="row g-3">
            <div class="col-lg-4 col-md-6">
                <label for="annee" class="form-label">Année</label>
                <input type="number" name="annee" id="annee" class="form-control" value="{{ $annee ?? date('Y') }}" min="2000" max="{{ date('Y') }}">
            </div>
            <div class="col-lg-4 col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Afficher</button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des totaux par mois -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <h4 class="card-title">Détails des Montants</h4>
            {{-- <div>
                <button class="btn btn-success export-excel">📊 Exporter en Excel</button>
                <button class="btn btn-danger export-pdf">📄 Exporter en PDF</button>
            </div> --}}
        </div>
        
        <div class="table-responsive">
            <table id="totaux-table" class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Mois</th>
                        <th>Total Montant (F CFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($montantsParMois as $montant)
                    <tr>
                        <td>{{ $montant->mois }}</td>
                        <td class="text-end">{{ number_format($montant->total_mois, 0, ',', ' ') }} F CFA</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th class="text-end">Total Général :</th>
                        <th id="total-sum" class="text-end"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@section('add-js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        let table = $('#totaux-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/French.json"
            },
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success' },
                { extend: 'pdfHtml5', text: '📄 PDF', className: 'btn btn-danger' },
                { extend: 'print', text: '🖨️ Imprimer', className: 'btn btn-primary' }
            ],
            responsive: true,
            pageLength: 10,
            drawCallback: function () {
                let api = this.api();
                let total = api.column(1, { page: 'all' }).data().reduce((a, b) => {
                    return parseFloat(a) + parseFloat(b.toString().replace(/\s/g, '').replace('F CFA', ''));
                }, 0);
                $('#total-sum').html(new Intl.NumberFormat('fr-FR').format(total) + ' F CFA');
            }
        });

        $('.export-excel').on('click', function() {
            table.button('.buttons-excel').trigger();
        });

        $('.export-pdf').on('click', function() {
            table.button('.buttons-pdf').trigger();
        });
    });
</script>
@stop
@endsection

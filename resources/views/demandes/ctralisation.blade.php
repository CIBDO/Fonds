@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Générer un PDF consolidé (Centralisation)</h5>
                </div>
                <div class="card-body">
                    <form id="monthly-pdf-form" action="" method="GET" target="_blank" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="mois" class="form-label">Mois</label>
                            <select name="mois" id="mois-select" class="form-select">
                                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $m)
                                    <option value="{{ $m }}" {{ request('mois', date('F')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="annee" class="form-label">Année</label>
                            <select name="annee" id="annee-select" class="form-select">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('annee', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf"></i> Générer PDF consolidé
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('monthly-pdf-form');
        const moisSelect = document.getElementById('mois-select');
        const anneeSelect = document.getElementById('annee-select');

        function updateFormAction() {
            const mois = moisSelect.value;
            const annee = anneeSelect.value;
            const baseUrl = "{{ url('demandes-fonds/mois') }}";
            form.action = `${baseUrl}/${mois}/${annee}/pdf`;
        }

        updateFormAction();
        moisSelect.addEventListener('change', updateFormAction);
        anneeSelect.addEventListener('change', updateFormAction);
    });
</script>
@endpush

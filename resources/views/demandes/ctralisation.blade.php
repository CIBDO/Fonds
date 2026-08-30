@extends('layouts.master')

@section('title', 'Centralisation - PDF Consolidé')

@section('content')

<x-vuexy.card title="PDF consolidé" icon="tabler-file-type-pdf">
    <form id="monthly-pdf-form" action="" method="GET" target="_blank" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="mois-select" class="form-label">Mois</label>
            <select name="mois" id="mois-select" class="form-select">
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $m)
                    <option value="{{ $m }}" {{ request('mois', date('F')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="annee-select" class="form-label">Année</label>
            <select name="annee" id="annee-select" class="form-select">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('annee', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">
                <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer PDF consolidé
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('monthly-pdf-form');
    const moisSelect = document.getElementById('mois-select');
    const anneeSelect = document.getElementById('annee-select');
    function updateFormAction() {
        form.action = "{{ url('demandes-fonds/mois') }}/" + moisSelect.value + "/" + anneeSelect.value + "/pdf";
    }
    updateFormAction();
    moisSelect.addEventListener('change', updateFormAction);
    anneeSelect.addEventListener('change', updateFormAction);
});
</script>
@endpush

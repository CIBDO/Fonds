@extends('layouts.master')

@section('title', 'Filtrage État Reversements/Recouvrements PCS')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.declarations.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour à la liste
    </a>
</div>

<div class="row g-6">
    <div class="col-lg-8">
        <x-vuexy.card title="Paramètres de filtrage" icon="tabler-calendar">
            <form id="filtreForm" method="GET" action="{{ route('pcs.declarations.declarations.etat-consolide.filtre') }}" target="_blank">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="programme" class="form-label">
                            <i class="icon-base ti tabler-world text-info me-1"></i>
                            Programme <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('programme') is-invalid @enderror"
                                id="programme"
                                name="programme"
                                required>
                            <option value="">Sélectionner un programme</option>
                            <option value="UEMOA" {{ old('programme', request('programme')) == 'UEMOA' ? 'selected' : '' }}>UEMOA</option>
                            <option value="AES" {{ old('programme', request('programme')) == 'AES' ? 'selected' : '' }}>AES</option>
                        </select>
                        @error('programme')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="annee" class="form-label">
                            <i class="icon-base ti tabler-calendar text-warning me-1"></i>
                            Année de référence <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               class="form-control @error('annee') is-invalid @enderror"
                               id="annee"
                               name="annee"
                               value="{{ old('annee', request('annee', date('Y'))) }}"
                               min="2020"
                               max="{{ date('Y') + 1 }}"
                               required>
                        @error('annee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="date_debut" class="form-label">
                            <i class="icon-base ti tabler-calendar-plus text-success me-1"></i>
                            Date de début <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control @error('date_debut') is-invalid @enderror"
                               id="date_debut"
                               name="date_debut"
                               value="{{ old('date_debut', request('date_debut', date('Y-01-01'))) }}"
                               required>
                        @error('date_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="date_fin" class="form-label">
                            <i class="icon-base ti tabler-calendar-minus text-danger me-1"></i>
                            Date de fin <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control @error('date_fin') is-invalid @enderror"
                               id="date_fin"
                               name="date_fin"
                               value="{{ old('date_fin', request('date_fin', date('Y-m-d'))) }}"
                               required>
                        @error('date_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="poste_id" class="form-label">
                            <i class="icon-base ti tabler-map-pin text-info me-1"></i>
                            Filtrer par poste
                        </label>
                        <select class="form-select @error('poste_id') is-invalid @enderror"
                                id="poste_id"
                                name="poste_id">
                            <option value="">Tous les postes</option>
                            @foreach($postes as $poste)
                                <option value="{{ $poste->id }}"
                                        {{ old('poste_id', request('poste_id')) == $poste->id ? 'selected' : '' }}>
                                    {{ $poste->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('poste_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="bureau_id" class="form-label">
                            <i class="icon-base ti tabler-building text-secondary me-1"></i>
                            Filtrer par bureau de douanes
                        </label>
                        <select class="form-select @error('bureau_id') is-invalid @enderror"
                                id="bureau_id"
                                name="bureau_id">
                            <option value="">Tous les bureaux</option>
                            @foreach($bureaux as $bureau)
                                <option value="{{ $bureau->id }}"
                                        {{ old('bureau_id', request('bureau_id')) == $bureau->id ? 'selected' : '' }}>
                                    {{ $bureau->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('bureau_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="mois" class="form-label">
                            <i class="icon-base ti tabler-calendar-event text-primary me-1"></i>
                            Filtrer par mois
                        </label>
                        <select class="form-select @error('mois') is-invalid @enderror"
                                id="mois"
                                name="mois">
                            <option value="">Tous les mois</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('mois', request('mois')) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        @error('mois')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="format" class="form-label">
                            <i class="icon-base ti tabler-file-export text-primary me-1"></i>
                            Format d'export
                        </label>
                        <select class="form-select" id="format" name="format">
                            <option value="pdf" selected>PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary" id="btnGenerer">
                        <i class="icon-base ti tabler-file-type-pdf me-1"></i>
                        Générer l'état PDF
                    </button>
                    <button type="button" class="btn btn-label-success" id="btnApercu" onclick="afficherApercu()">
                        <i class="icon-base ti tabler-eye me-1"></i>
                        Aperçu des données
                    </button>
                    <button type="button" class="btn btn-label-secondary" onclick="resetForm()">
                        <i class="icon-base ti tabler-rotate me-1"></i>
                        Réinitialiser
                    </button>
                </div>
            </form>
        </x-vuexy.card>
    </div>

    <div class="col-lg-4">
        <x-vuexy.card title="Informations" icon="tabler-info-circle" class="mb-6">
            <div class="alert alert-info mb-3">
                <h6 class="alert-heading mb-2"><i class="icon-base ti tabler-bulb me-1"></i> Comment utiliser :</h6>
                <ul class="mb-0 ps-3">
                    <li>Sélectionnez le programme (UEMOA/AES)</li>
                    <li>Choisissez l'année et la période</li>
                    <li>Optionnel : filtrez par poste, bureau ou mois</li>
                    <li>Cliquez sur "Générer l'état PDF"</li>
                </ul>
            </div>
            <div class="alert alert-warning mb-0">
                <h6 class="alert-heading mb-2"><i class="icon-base ti tabler-alert-triangle me-1"></i> Note importante :</h6>
                <p class="mb-0">L'état PDF sera généré en format paysage avec les données correspondant aux critères sélectionnés.</p>
            </div>
        </x-vuexy.card>

        <x-vuexy.card title="Statistiques rapides" icon="tabler-chart-bar">
            <div id="statsContainer">
                <div class="text-center text-body-secondary py-3">
                    <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                    <p class="mb-0">Chargement des statistiques...</p>
                </div>
            </div>
        </x-vuexy.card>
    </div>
</div>

<div class="modal fade" id="apercuModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ti tabler-eye me-2"></i>
                    Aperçu des données filtrées
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="apercuContent">
                    <div class="text-center text-body-secondary py-4">
                        <div class="spinner-border mb-2" role="status"></div>
                        <p class="mb-0">Chargement de l'aperçu...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="genererAvecParametres()">
                    <i class="icon-base ti tabler-file-type-pdf me-1"></i>
                    Générer le PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const btnGenerer = document.getElementById('btnGenerer');

    function validateDates() {
        const debut = new Date(dateDebut.value);
        const fin = new Date(dateFin.value);

        if (debut > fin) {
            dateFin.setCustomValidity('La date de fin doit être postérieure à la date de début');
            btnGenerer.disabled = true;
        } else {
            dateFin.setCustomValidity('');
            btnGenerer.disabled = false;
        }
    }

    dateDebut.addEventListener('change', validateDates);
    dateFin.addEventListener('change', validateDates);

    chargerStatistiques();

    [dateDebut, dateFin, document.getElementById('poste_id'), document.getElementById('bureau_id'), document.getElementById('mois'), document.getElementById('programme')].forEach(element => {
        element.addEventListener('change', chargerStatistiques);
    });
});

function resetForm() {
    document.getElementById('filtreForm').reset();
    document.getElementById('date_debut').value = '{{ date("Y-01-01") }}';
    document.getElementById('date_fin').value = '{{ date("Y-m-d") }}';
    document.getElementById('annee').value = '{{ date("Y") }}';
    document.getElementById('programme').value = '';
    document.getElementById('poste_id').value = '';
    document.getElementById('bureau_id').value = '';
    document.getElementById('mois').value = '';
    document.getElementById('format').value = 'pdf';
    chargerStatistiques();
}

function chargerStatistiques() {
    const params = new URLSearchParams({
        programme: document.getElementById('programme').value,
        annee: document.getElementById('annee').value,
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        poste_id: document.getElementById('poste_id').value,
        bureau_id: document.getElementById('bureau_id').value,
        mois: document.getElementById('mois').value,
        _token: '{{ csrf_token() }}'
    });

    fetch('{{ route("pcs.declarations.declarations.stats-rapides") }}?' + params)
        .then(response => response.json())
        .then(data => {
            document.getElementById('statsContainer').innerHTML = `
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-1">${data.total_declarations}</h4>
                            <p class="text-body-secondary small mb-0">Total déclarations</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-1">${data.montant_recouvrement}</h4>
                            <p class="text-body-secondary small mb-0">Total recouvrements</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-1">${data.montant_reversement}</h4>
                            <p class="text-body-secondary small mb-0">Total reversements</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-1">${data.postes_actifs}</h4>
                            <p class="text-body-secondary small mb-0">Postes actifs</p>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(() => {
            document.getElementById('statsContainer').innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                    Erreur lors du chargement des statistiques
                </div>
            `;
        });
}

function afficherApercu() {
    const modal = new bootstrap.Modal(document.getElementById('apercuModal'));
    modal.show();

    const params = new URLSearchParams({
        programme: document.getElementById('programme').value,
        annee: document.getElementById('annee').value,
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        poste_id: document.getElementById('poste_id').value,
        bureau_id: document.getElementById('bureau_id').value,
        mois: document.getElementById('mois').value,
        apercu: '1',
        _token: '{{ csrf_token() }}'
    });

    fetch('{{ route("pcs.declarations.declarations.apercu") }}?' + params)
        .then(response => response.text())
        .then(html => {
            document.getElementById('apercuContent').innerHTML = html;
            document.querySelectorAll('#apercuContent [data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        })
        .catch(() => {
            document.getElementById('apercuContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                    Erreur lors du chargement de l'aperçu
                </div>
            `;
        });
}

function genererAvecParametres() {
    document.getElementById('filtreForm').submit();
}
</script>
@endpush

@extends('layouts.master')

@section('title', 'Nouvelle Autre Demande')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Informations des Demandes" icon="tabler-file">
    <x-slot:actions>
        <button type="button" class="btn btn-primary btn-sm" id="ajouterLigne">
            <i class="icon-base ti tabler-plus me-1"></i>Ajouter une ligne
        </button>
    </x-slot:actions>

    <form action="{{ route('pcs.autres-demandes.store') }}" method="POST" id="formDemandes" enctype="multipart/form-data">
        @csrf

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label for="annee_globale" class="form-label fw-bold">
                    Année (pour toutes les demandes) <span class="text-danger">*</span>
                </label>
                <select name="annee_globale"
                        id="annee_globale"
                        class="form-select @error('annee_globale') is-invalid @enderror"
                        required>
                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                        <option value="{{ $i }}" {{ old('annee_globale', date('Y')) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                @error('annee_globale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="date_globale" class="form-label fw-bold">
                    Date (pour toutes les demandes) <span class="text-danger">*</span>
                </label>
                <input type="date"
                       class="form-control @error('date_globale') is-invalid @enderror"
                       id="date_globale"
                       name="date_globale"
                       value="{{ old('date_globale', date('Y-m-d')) }}"
                       required>
                @error('date_globale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="my-4">

        <div id="lignes-demandes">
            <div class="ligne-demande border rounded p-3 mb-3 bg-label-secondary position-relative" data-index="0">
                <div class="position-absolute top-0 end-0 m-2">
                    <button type="button" class="btn btn-danger btn-sm btn-icon supprimer-ligne" style="display: none;">
                        <i class="icon-base ti tabler-trash"></i>
                    </button>
                </div>

                <h6 class="fw-bold mb-3">
                    <i class="icon-base ti tabler-file-text me-2"></i>Demande <span class="numero-ligne">1</span>
                </h6>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">
                            Désignation <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               name="demandes[0][designation]"
                               placeholder="Ex: Demande d'avance pour mission, Achat matériel, etc."
                               required>
                        <small class="text-body-secondary">Nature ou objet de la demande</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            Montant (FCFA) <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control form-control-lg montant-ligne montant-input"
                               name="demandes[0][montant]"
                               inputmode="decimal"
                               data-min="0"
                               placeholder="0"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Observation</label>
                        <textarea name="demandes[0][observation]"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Détails complémentaires..."></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">
                            <i class="icon-base ti tabler-paperclip me-1"></i>Justificatif (fichier)
                        </label>
                        <input type="file"
                               class="form-control"
                               name="demandes[0][preuve_paiement]"
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               id="preuve_0">
                        <small class="text-body-secondary">PDF, images ou Word. Optionnel.</small>
                    </div>
                </div>
            </div>
        </div>

        <x-vuexy.card class="mb-4 border-primary">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-body-secondary">Nombre de demandes</h6>
                    <h4 class="text-primary" id="nombre-demandes">1</h4>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-body-secondary">Montant total</h6>
                    <h4 class="text-primary" id="montant-total">0 FCFA</h4>
                </div>
            </div>
        </x-vuexy.card>

        <div class="alert alert-light border mb-4">
            <strong>Poste :</strong> {{ $poste->nom }}
        </div>

        @if($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <h6 class="alert-heading">Erreurs de validation :</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
        @endif

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-lg">
                <i class="icon-base ti tabler-x me-1"></i>Annuler
            </a>
            <button type="submit" name="action" value="soumettre" class="btn btn-primary btn-lg">
                <i class="icon-base ti tabler-send me-1"></i>Soumettre
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    function parseMontant(str) {
        if (str === '' || str == null) return '';
        const s = String(str).replace(/\s/g, '').replace(',', '.');
        const num = parseFloat(s);
        return isNaN(num) ? '' : String(num);
    }
    function formatMontant(str) {
        const parsed = parseMontant(str);
        if (parsed === '') return '';
        const parts = parsed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.length > 1 ? parts[0] + ',' + parts[1] : parts[0];
    }
    function initMontantInputs(container) {
        const scope = container || document;
        scope.querySelectorAll('.montant-input').forEach(function(input) {
            if (input.dataset.montantInit) return;
            input.dataset.montantInit = '1';
            input.addEventListener('blur', function() {
                const v = this.value.trim();
                if (v) this.value = formatMontant(v);
            });
            input.addEventListener('focus', function() {
                const v = this.value.trim();
                if (v) this.value = parseMontant(v).replace('.', ',');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        let ligneIndex = 1;

        initMontantInputs();
        document.querySelectorAll('.montant-input').forEach(function(input) {
            if (input.value && input.value.trim()) input.value = formatMontant(input.value);
        });

        const btnAjouter = document.getElementById('ajouterLigne');
        if (!btnAjouter) return;

        const container = document.getElementById('lignes-demandes');
        if (!container) return;

        btnAjouter.addEventListener('click', function(e) {
            e.preventDefault();

            const template = container.querySelector('.ligne-demande');
            if (!template) return;

            const nouvelleLigne = template.cloneNode(true);

            nouvelleLigne.setAttribute('data-index', ligneIndex);

            const numeroLigne = nouvelleLigne.querySelector('.numero-ligne');
            if (numeroLigne) numeroLigne.textContent = ligneIndex + 1;

            nouvelleLigne.querySelectorAll('input, textarea').forEach(function(input) {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, '[' + ligneIndex + ']'));
                    if (input.type !== 'file') input.value = '';
                }
                input.removeAttribute('data-montant-init');
                if (input.type === 'file') input.value = '';
            });
            var preuveInput = nouvelleLigne.querySelector('input[type="file"][name*="preuve_paiement"]');
            if (preuveInput) {
                preuveInput.id = 'preuve_' + ligneIndex;
            }

            const btnSupprimer = nouvelleLigne.querySelector('.supprimer-ligne');
            if (btnSupprimer) btnSupprimer.style.display = 'inline-block';

            container.appendChild(nouvelleLigne);
            initMontantInputs(nouvelleLigne);

            ligneIndex++;
            mettreAJourBoutonSupprimer();
            calculerTotal();
        });

        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.closest('.supprimer-ligne')) {
                    e.preventDefault();
                    const ligne = e.target.closest('.ligne-demande');
                    if (ligne) {
                        ligne.remove();
                        mettreAJourNumeros();
                        mettreAJourBoutonSupprimer();
                        calculerTotal();
                    }
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('montant-ligne')) calculerTotal();
            });
        }

        document.getElementById('formDemandes').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        document.getElementById('formDemandes').addEventListener('submit', function() {
            document.querySelectorAll('.montant-input').forEach(function(input) {
                if (input.value && input.value.trim()) input.value = parseMontant(input.value);
            });
        });

        function mettreAJourNumeros() {
            document.querySelectorAll('.ligne-demande').forEach(function(ligne, index) {
                const numeroLigne = ligne.querySelector('.numero-ligne');
                if (numeroLigne) {
                    numeroLigne.textContent = index + 1;
                }
            });
        }

        function mettreAJourBoutonSupprimer() {
            const lignes = document.querySelectorAll('.ligne-demande');
            lignes.forEach(function(ligne) {
                const btnSupprimer = ligne.querySelector('.supprimer-ligne');
                if (btnSupprimer) {
                    if (lignes.length === 1) {
                        btnSupprimer.style.display = 'none';
                    } else {
                        btnSupprimer.style.display = 'inline-block';
                    }
                }
            });
        }

        function calculerTotal() {
            let total = 0;

            document.querySelectorAll('.montant-ligne').forEach(function(input) {
                const valeur = parseFloat(parseMontant(input.value)) || 0;
                total += valeur;
            });

            const nombreDemandes = document.getElementById('nombre-demandes');
            const montantTotal = document.getElementById('montant-total');

            if (nombreDemandes) {
                nombreDemandes.textContent = document.querySelectorAll('.ligne-demande').length;
            }

            if (montantTotal) {
                montantTotal.textContent = new Intl.NumberFormat('fr-FR', {
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(total) + ' FCFA';
            }
        }

        calculerTotal();
    }
})();
</script>
@endpush

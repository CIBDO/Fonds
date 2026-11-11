@php
    $valeur = function (string $champ, $defaut = null) use ($banque) {
        return old($champ, $banque?->$champ ?? $defaut);
    };
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
        <input type="text"
               name="code"
               id="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ $valeur('code') }}"
               maxlength="50"
               required
               placeholder="Ex. BDM">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="nom">Nom <span class="text-danger">*</span></label>
        <input type="text"
               name="nom"
               id="nom"
               class="form-control @error('nom') is-invalid @enderror"
               value="{{ $valeur('nom') }}"
               maxlength="255"
               required
               placeholder="Ex. BDM - BANQUE DE DEVELOPPEMENT DU MALI">
        @error('nom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="actif" value="0">
            <input class="form-check-input"
                   type="checkbox"
                   role="switch"
                   id="actif"
                   name="actif"
                   value="1"
                   @checked((string) $valeur('actif', true) === '1' || $valeur('actif', true) === true)>
            <label class="form-check-label" for="actif">Banque active (proposée dans les VIR)</label>
        </div>
    </div>
</div>

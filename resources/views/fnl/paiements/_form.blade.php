@php
    $valeur = function (string $champ, $defaut = null) use ($paiement) {
        return old($champ, $paiement?->$champ ?? $defaut);
    };
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Mois <span class="text-danger">*</span></label>
        <select name="mois" class="form-select" required>
            @foreach($moisList as $moisNum => $moisNom)
                <option value="{{ $moisNum }}" {{ (int) $valeur('mois', date('n')) === (int) $moisNum ? 'selected' : '' }}>{{ $moisNom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Année <span class="text-danger">*</span></label>
        <select name="annee" class="form-select" required>
            @foreach($annees as $anneeOption)
                <option value="{{ $anneeOption }}" {{ (int) $valeur('annee', date('Y')) === (int) $anneeOption ? 'selected' : '' }}>{{ $anneeOption }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date de paiement</label>
        <input type="date" name="date_paiement" class="form-control" value="{{ $valeur('date_paiement') ? \Illuminate\Support\Carbon::parse($valeur('date_paiement'))->format('Y-m-d') : '' }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Retenue FNL <span class="text-danger">*</span></label>
        @php
            $retenueBrute = str_replace(["\u{00A0}", ' '], '', (string) $valeur('retenue_fnl', 0));
            $retenueBrute = str_replace(',', '.', $retenueBrute);
            $retenueAffichee = is_numeric($retenueBrute)
                ? number_format((float) $retenueBrute, fmod((float) $retenueBrute, 1) == 0.0 ? 0 : 2, ',', ' ')
                : $valeur('retenue_fnl', '');
        @endphp
        <input type="text" name="retenue_fnl" id="retenue_fnl" class="form-control" inputmode="decimal" required value="{{ $retenueAffichee }}" autocomplete="off">
    </div>
    <div class="col-md-6">
        <label class="form-label">Référence de paiement</label>
        <input type="text" name="reference_paiement" class="form-control" value="{{ $valeur('reference_paiement') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Preuve de paiement</label>
        <input type="file" name="preuve_paiement" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        @if($paiement?->preuve_paiement)
            <small class="text-body-secondary">Un fichier est déjà joint. Un nouveau fichier le remplace.</small>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label">Observation</label>
        <textarea name="observation" class="form-control" rows="3">{{ $valeur('observation') }}</textarea>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('retenue_fnl');
    if (!input) return;

    function formatMontant(valeur) {
        const brut = String(valeur).replace(/\s/g, '').replace(',', '.');
        if (brut === '' || brut === '.') return '';
        const negatif = brut.startsWith('-') ? '-' : '';
        const corps = negatif ? brut.slice(1) : brut;
        const morceaux = corps.split('.');
        const entier = (morceaux[0] || '').replace(/\D/g, '');
        const groupe = entier.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        if (morceaux.length > 1) {
            return negatif + groupe + ',' + morceaux[1].replace(/\D/g, '').slice(0, 2);
        }
        return negatif + groupe;
    }

    input.addEventListener('input', function () {
        const debut = this.selectionStart;
        const avant = this.value.length;
        this.value = formatMontant(this.value);
        const delta = this.value.length - avant;
        const pos = Math.max(0, (debut || 0) + delta);
        this.setSelectionRange(pos, pos);
    });

    input.value = formatMontant(input.value);

    const form = input.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            input.value = input.value.replace(/\s/g, '').replace(',', '.');
        });
    }
})();
</script>
@endpush

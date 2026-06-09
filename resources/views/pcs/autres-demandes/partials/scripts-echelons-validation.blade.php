<script>
(function () {
    function parseMontant(val) {
        const n = parseFloat(String(val).replace(/\s/g, '').replace(',', '.'));
        return isNaN(n) ? 0 : n;
    }

    function initFormVersement(form) {
        if (form.dataset.versementInit === '1') return;
        form.dataset.versementInit = '1';

        const versementInput = form.querySelector('.montant-versement-input');
        const plafondInput = form.querySelector('.montant-plafond-input');
        const verserTotal = form.querySelector('.btn-verser-total');

        if (!versementInput) return;

        function getRestant() {
            const plafond = parseMontant(plafondInput ? plafondInput.value : 0);
            const dejaVerse = parseMontant(versementInput.dataset.dejaVerse || 0);
            const fromData = parseMontant(versementInput.dataset.montantRestant);
            if (fromData > 0) return fromData;
            return Math.max(0, plafond - dejaVerse);
        }

        if (verserTotal) {
            verserTotal.addEventListener('change', function () {
                if (this.checked) {
                    const restant = getRestant();
                    versementInput.value = restant > 0 ? restant : '';
                    versementInput.max = restant;
                }
            });
        }

        if (plafondInput && !plafondInput.readOnly) {
            plafondInput.addEventListener('input', function () {
                const plafond = parseMontant(plafondInput.value);
                versementInput.max = plafond;
                versementInput.dataset.montantRestant = plafond;
                const restantEl = form.closest('.modal-content')?.querySelector('.montant-restant-display');
                if (restantEl) {
                    restantEl.textContent = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(plafond) + ' FCFA';
                }
            });
        }

        form.addEventListener('submit', function (e) {
            const versement = parseMontant(versementInput.value);
            const restant = parseMontant(versementInput.dataset.montantRestant) || getRestant();

            if (versement <= 0) {
                e.preventDefault();
                alert('Le montant du versement doit être supérieur à 0.');
                return;
            }

            if (restant > 0 && versement > restant + 0.01) {
                e.preventDefault();
                alert('Le versement ne peut pas dépasser le montant restant (' + new Intl.NumberFormat('fr-FR').format(restant) + ' FCFA).');
            }
        });
    }

    document.querySelectorAll('.form-validation-versement').forEach(initFormVersement);

    document.addEventListener('shown.bs.modal', function (e) {
        const form = e.target.querySelector('.form-validation-versement');
        if (form) initFormVersement(form);
    });
})();
</script>

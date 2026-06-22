<script>
(function () {
    function parseMontant(val) {
        const n = parseFloat(String(val).replace(/\s/g, '').replace(',', '.'));
        return isNaN(n) ? 0 : n;
    }

    function formatFcfa(n) {
        return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(n) + ' FCFA';
    }

    function initFormVersement(form) {
        if (form.dataset.versementInit === '1') return;
        form.dataset.versementInit = '1';

        const versementInput = form.querySelector('.montant-versement-input');
        const plafondInput = form.querySelector('.montant-plafond-input');
        const verserTotal = form.querySelector('.btn-verser-total');
        const restantEl = form.closest('.modal-content')?.querySelector('.montant-restant-display');

        if (!versementInput) return;

        const dejaVerse = parseMontant(versementInput.dataset.dejaVerse || 0);

        function getPlafond() {
            return parseMontant(plafondInput ? plafondInput.value : 0);
        }

        function getRestant() {
            return Math.max(0, getPlafond() - dejaVerse);
        }

        function updateRestantDisplay() {
            if (!restantEl) return;
            const restant = getRestant();
            restantEl.textContent = restant > 0 ? formatFcfa(restant) : '—';
            versementInput.dataset.montantRestant = restant;
        }

        if (verserTotal) {
            verserTotal.addEventListener('change', function () {
                if (this.checked) {
                    const restant = getRestant();
                    versementInput.value = restant > 0 ? restant : '';
                }
            });
        }

        if (plafondInput) {
            plafondInput.addEventListener('input', function () {
                const plafond = getPlafond();
                const minPlafond = parseMontant(plafondInput.min || 0);
                if (plafond < minPlafond) {
                    plafondInput.value = minPlafond;
                }
                updateRestantDisplay();
            });
        }

        versementInput.addEventListener('input', function () {
            const versement = parseMontant(versementInput.value);
            const plafond = getPlafond();
            const totalApres = dejaVerse + versement;

            if (versement > 0 && totalApres > plafond + 0.01 && plafondInput) {
                plafondInput.value = totalApres;
                updateRestantDisplay();
            }
        });

        form.addEventListener('submit', function (e) {
            const versement = parseMontant(versementInput.value);
            const plafond = getPlafond();
            const minPlafond = parseMontant(plafondInput ? plafondInput.min : 0);

            if (versement <= 0) {
                e.preventDefault();
                alert('Le montant du versement doit être supérieur à 0.');
                return;
            }

            if (dejaVerse + versement > plafond + 0.01 && plafondInput) {
                plafondInput.value = dejaVerse + versement;
            }

            if (parseMontant(plafondInput ? plafondInput.value : 0) < minPlafond) {
                e.preventDefault();
                alert('Le montant accordé ne peut pas être inférieur au total déjà versé.');
            }
        });

        updateRestantDisplay();
    }

    document.querySelectorAll('.form-validation-versement').forEach(initFormVersement);

    document.addEventListener('shown.bs.modal', function (e) {
        const form = e.target.querySelector('.form-validation-versement');
        if (form) initFormVersement(form);
    });
})();
</script>

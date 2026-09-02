@include('partials.demandes.table-salaires-styles')

<div class="table-salaires-wrapper">
    <table class="table table-bordered table-salaires table-hover">
        <thead>
            <tr>
                <th style="width: 22%;">
                    <i class="ti tabler-users me-1"></i>Catégorie Personnel
                </th>
                <th style="width: 15%;">
                    <i class="ti tabler-cash me-1"></i>Salaire Net
                </th>
                <th style="width: 15%;">
                    <i class="ti tabler-receipt me-1"></i>Revers/Salaire
                </th>
                <th style="width: 15%;">
                    <i class="ti tabler-calculator me-1"></i>Total Mois Courant
                </th>
                <th style="width: 16%;">
                    <i class="ti tabler-history me-1"></i>Salaire Mois Antérieur
                </th>
                <th style="width: 17%;">
                    <i class="ti tabler-trending-up me-1"></i>Écart (Demande)
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- Fonctionnaires BCS -->
            <tr data-category="fonctionnaires-bcs">
                <td>
                    <i class="ti tabler-user me-2 text-body-secondary"></i>Fonctionnaires BCS
                </td>
                <td><input type="text" name="fonctionnaires_bcs_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="fonctionnaires_bcs_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="fonctionnaires_bcs_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire" data-champ="fonctionnaires_bcs_total_courant" value="{{ $previousData?->fonctionnaires_bcs_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnel Collectivité Santé -->
            <tr data-category="collectivite-sante">
                <td>
                    <i class="ti tabler-heart me-2 text-body-secondary"></i>Personnel Collectivité Santé
                </td>
                <td><input type="text" name="collectivite_sante_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="collectivite_sante_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="collectivite_sante_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire" data-champ="collectivite_sante_total_courant" value="{{ $previousData?->collectivite_sante_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="collectivite_sante_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnel Collectivité Éducation -->
            <tr data-category="collectivite-education">
                <td>
                    <i class="ti tabler-school me-2 text-body-secondary"></i>Personnel Collectivité Éducation
                </td>
                <td><input type="text" name="collectivite_education_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="collectivite_education_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="collectivite_education_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire" data-champ="collectivite_education_total_courant" value="{{ $previousData?->collectivite_education_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="collectivite_education_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnels Saisonniers -->
            <tr data-category="personnels-saisonniers">
                <td>
                    <i class="ti tabler-calendar me-2 text-body-secondary"></i>Personnels Saisonniers
                </td>
                <td><input type="text" name="personnels_saisonniers_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="personnels_saisonniers_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="personnels_saisonniers_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire" data-champ="personnels_saisonniers_total_courant" value="{{ $previousData?->personnels_saisonniers_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="personnels_saisonniers_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- EPN -->
            <tr data-category="epn">
                <td>
                    <i class="ti tabler-building me-2 text-body-secondary"></i>EPN
                </td>
                <td><input type="text" name="epn_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="epn_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="epn_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="epn_salaire_ancien" class="form-control ancien_salaire" data-champ="epn_total_courant" value="{{ $previousData?->epn_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="epn_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="ced">
                <td>
                    <i class="ti tabler-building-bank me-2 text-body-secondary"></i>CED
                </td>
                <td><input type="text" name="ced_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="ced_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="ced_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="ced_salaire_ancien" class="form-control ancien_salaire" data-champ="ced_total_courant" value="{{ $previousData?->ced_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="ced_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="ecom">
                <td>
                    <i class="ti tabler-building-store me-2 text-body-secondary"></i>ECOM
                </td>
                <td><input type="text" name="ecom_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="ecom_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="ecom_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="ecom_salaire_ancien" class="form-control ancien_salaire" data-champ="ecom_total_courant" value="{{ $previousData?->ecom_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="ecom_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="cfp-cpam">
                <td>
                    <i class="ti tabler-certificate me-2 text-body-secondary"></i>CFP-CPAM
                </td>
                <td><input type="text" name="cfp_cpam_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="cfp_cpam_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="cfp_cpam_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" data-champ="cfp_cpam_total_courant" value="{{ $previousData?->cfp_cpam_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="cfp_cpam_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Ligne de total automatique -->
            <tr class="total-row">
                <td>
                    <i class="ti tabler-sum me-2"></i><strong>TOTAL GÉNÉRAL</strong>
                </td>
                <td><input type="text" name="total_net" class="form-control" id="total_net" readonly></td>
                <td><input type="text" name="total_revers" class="form-control" id="total_revers" readonly></td>
                <td><input type="text" name="total_courant" class="form-control" id="total_courant" readonly></td>
                <td><input type="text" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
                <td><input type="text" name="total_demande" class="form-control" id="total_demande" readonly></td>
            </tr>
        </tbody>
    </table>
    <div id="salaire-anterieur-info" class="px-3 pb-3 small text-body-secondary"></div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        }

        function unformatNumber(formattedNumber) {
            if (typeof formattedNumber === 'string') {
                return parseFloat(formattedNumber.replace(/\s/g, '').replace(',', '.')) || 0;
            }
            return formattedNumber || 0;
        }

        const netFields = document.querySelectorAll('.net');
        const reversFields = document.querySelectorAll('.revers');
        const totalCourantFields = document.querySelectorAll('.total_courant');
        const salaireAncienFields = document.querySelectorAll('.ancien_salaire');
        const totalDemandeFields = document.querySelectorAll('.total_demande');
        const totalNetField = document.getElementById('total_net');
        const totalReversField = document.getElementById('total_revers');
        const totalCourantField = document.getElementById('total_courant');
        const totalSalaireAncienField = document.getElementById('total_salaire_ancien');
        const totalDemandeField = document.getElementById('total_demande');
        const montantDisponibleField = document.getElementById('montant_disponible');
        const soldeField = document.getElementById('solde');
        const moisField = document.getElementById('mois');
        const anneeField = document.getElementById('annee');
        const infoField = document.getElementById('salaire-anterieur-info');
        const salairePrecedentUrl = @json(route('demandes-fonds.salaire-mois-precedent'));

        function calculateTotals() {
            let totalNet = 0;
            let totalRevers = 0;
            let totalCourant = 0;
            let totalSalaireAncien = 0;
            let totalDemande = 0;

            netFields.forEach((field, index) => {
                const net = unformatNumber(field.value);
                const revers = unformatNumber(reversFields[index].value);
                const courant = net + revers;
                const ancien = unformatNumber(salaireAncienFields[index].value);
                const demande = courant - ancien;

                totalCourantFields[index].value = formatNumber(courant);
                totalDemandeFields[index].value = formatNumber(demande);

                totalNet += net;
                totalRevers += revers;
                totalCourant += courant;
                totalSalaireAncien += ancien;
                totalDemande += demande;
            });

            totalNetField.value = formatNumber(totalNet);
            totalReversField.value = formatNumber(totalRevers);
            totalCourantField.value = formatNumber(totalCourant);
            totalSalaireAncienField.value = formatNumber(totalSalaireAncien);
            totalDemandeField.value = formatNumber(totalDemande);

            calculateSolde();
        }

        function calculateSolde() {
            const montantDisponible = unformatNumber(montantDisponibleField.value);
            const totalCourant = unformatNumber(totalCourantField.value);
            const solde = totalCourant - montantDisponible;
            soldeField.value = formatNumber(solde);
        }

        function handleInput(e) {
            const value = unformatNumber(e.target.value);
            e.target.value = formatNumber(value);
            calculateTotals();
        }

        netFields.forEach(field => field.addEventListener('input', handleInput));
        reversFields.forEach(field => field.addEventListener('input', handleInput));
        salaireAncienFields.forEach(field => field.addEventListener('input', handleInput));
        montantDisponibleField.addEventListener('input', handleInput);

        function appliquerSalairesAnterieurs(totaux) {
            salaireAncienFields.forEach(function (field) {
                const champ = field.dataset.champ;
                const valeur = champ && totaux[champ] !== undefined ? totaux[champ] : 0;
                field.value = formatNumber(valeur);
            });
            calculateTotals();
        }

        function libelleMois(mois) {
            const labels = { Fevrier: 'Février', Aout: 'Août', Decembre: 'Décembre' };
            return labels[mois] || mois;
        }

        let chargementSalaire = null;

        function chargerSalaireMoisPrecedent() {
            if (!moisField || !anneeField || !salairePrecedentUrl) {
                return;
            }

            const mois = moisField.value;
            const annee = anneeField.value;
            const posteInput = document.querySelector('input[name="poste_id"]');
            const posteId = posteInput ? posteInput.value : '';

            if (!mois || !annee) {
                return;
            }

            if (infoField) {
                infoField.textContent = 'Chargement du salaire du mois antérieur…';
            }

            if (chargementSalaire) {
                chargementSalaire.abort();
            }
            chargementSalaire = new AbortController();

            const params = new URLSearchParams({ mois, annee });
            if (posteId) {
                params.set('poste_id', posteId);
            }

            fetch(salairePrecedentUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: chargementSalaire.signal,
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    appliquerSalairesAnterieurs(data.totaux || {});

                    if (!infoField) {
                        return;
                    }

                    if (data.trouve && data.periode) {
                        infoField.textContent = 'Salaire antérieur repris de '
                            + libelleMois(data.periode.mois) + ' ' + data.periode.annee + '.';
                    } else if (data.periode) {
                        infoField.textContent = data.message
                            || ('Aucune demande pour ' + libelleMois(data.periode.mois) + ' ' + data.periode.annee + '.');
                    } else {
                        infoField.textContent = data.message || 'Aucune donnée antérieure disponible.';
                    }
                })
                .catch(function (error) {
                    if (error.name !== 'AbortError' && infoField) {
                        infoField.textContent = 'Impossible de charger le salaire du mois antérieur.';
                    }
                });
        }

        if (moisField && anneeField) {
            moisField.addEventListener('change', chargerSalaireMoisPrecedent);
            anneeField.addEventListener('change', chargerSalaireMoisPrecedent);
            chargerSalaireMoisPrecedent();
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const numericFields = document.querySelectorAll('.net, .revers, .total_courant, .ancien_salaire, .total_demande, #total_net, #total_revers, #total_courant, #total_salaire_ancien, #total_demande, #montant_disponible, #solde');

            numericFields.forEach(field => {
                field.value = unformatNumber(field.value);
            });
        });

        calculateTotals();
    });
    </script>
@endpush

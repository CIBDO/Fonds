<style>
    /* Tableau moderne et responsive */
    .table-salaires-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-salaires {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .table-salaires th {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
        color: white;
        font-weight: 600;
        padding: 1rem 0.75rem;
        text-align: center;
        vertical-align: middle;
        border: none;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .table-salaires th i {
        margin-right: 0.5rem;
        opacity: 0.9;
    }

    .table-salaires tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .table-salaires tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.005);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .table-salaires tbody tr.total-row {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        font-weight: 700;
        border-top: 3px solid #198754;
        border-bottom: 3px solid #198754;
    }

    .table-salaires tbody tr.total-row:hover {
        transform: none;
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }

    .table-salaires tbody tr.total-row td {
        padding: 1rem 0.75rem;
        font-size: 1rem;
    }

    .table-salaires td {
        padding: 0.75rem;
        text-align: center;
        vertical-align: middle;
    }

    .table-salaires td:first-child {
        font-weight: 600;
        text-align: left;
        color: #495057;
        background-color: #f8f9fa;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .table-salaires tbody tr:hover td:first-child {
        background-color: #e9ecef;
    }

    .table-salaires input[type="text"] {
        width: 100%;
        padding: 0.5rem;
        border: 2px solid #dee2e6;
        border-radius: 0.375rem;
        text-align: right;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: white;
    }

    .table-salaires input[type="text"]:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
        outline: none;
        transform: scale(1.02);
        background-color: #f0fff4;
    }

    .table-salaires input[type="text"]:read-only {
        background-color: #e9ecef;
        color: #495057;
        font-weight: 600;
        border-color: #ced4da;
        cursor: not-allowed;
    }

    .table-salaires tbody tr.total-row input {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border: 2px solid #198754;
        font-weight: 700;
        color: #198754;
        font-size: 1rem;
    }

    /* Badge pour les catégories */
    .categorie-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: #0d6efd;
        color: white;
        border-radius: 0.25rem;
        font-size: 0.85rem;
        margin-left: 0.5rem;
    }

    /* Animation pour les champs modifiés */
    @keyframes highlightField {
        0%, 100% { background-color: white; }
        50% { background-color: #d1f2eb; }
    }

    .table-salaires input.field-changed {
        animation: highlightField 0.5s ease;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .table-salaires {
            font-size: 0.85rem;
        }

        .table-salaires th, .table-salaires td {
            padding: 0.5rem;
        }

        .table-salaires input[type="text"] {
            padding: 0.4rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 768px) {
        .table-salaires th {
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
        }

        .table-salaires td {
            padding: 0.5rem 0.25rem;
        }

        .table-salaires input[type="text"] {
            padding: 0.3rem;
            font-size: 0.8rem;
        }
    }

    /* Info tooltip */
    .info-tooltip {
        position: relative;
        display: inline-block;
        cursor: help;
    }

    .info-tooltip .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.75rem;
    }

    .info-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class="table-salaires-wrapper">
    <table class="table table-salaires table-hover">
        <thead>
            <tr>
                <th style="width: 22%;">
                    <i class="fas fa-users"></i>Catégorie Personnel
                </th>
                <th style="width: 15%;">
                    <i class="fas fa-money-bill-wave"></i>Salaire Net
                </th>
                <th style="width: 15%;">
                    <i class="fas fa-hand-holding-usd"></i>Revers/Salaire
                </th>
                <th style="width: 15%;">
                    <i class="fas fa-calculator"></i>Total Mois Courant
                </th>
                <th style="width: 16%;">
                    <i class="fas fa-history"></i>Salaire Mois Antérieur
                </th>
                <th style="width: 17%;">
                    <i class="fas fa-chart-line"></i>Écart (Demande)
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- Fonctionnaires BCS -->
            <tr data-category="fonctionnaires-bcs">
                <td>
                    <i class="fas fa-user-tie text-primary me-2"></i>Fonctionnaires BCS
                </td>
                <td><input type="text" name="fonctionnaires_bcs_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="fonctionnaires_bcs_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="fonctionnaires_bcs_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->fonctionnaires_bcs_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnel Collectivité Santé -->
            <tr data-category="collectivite-sante">
                <td>
                    <i class="fas fa-heartbeat text-danger me-2"></i>Personnel Collectivité Santé
                </td>
                <td><input type="text" name="collectivite_sante_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="collectivite_sante_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="collectivite_sante_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->collectivite_sante_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="collectivite_sante_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnel Collectivité Éducation -->
            <tr data-category="collectivite-education">
                <td>
                    <i class="fas fa-graduation-cap text-info me-2"></i>Personnel Collectivité Éducation
                </td>
                <td><input type="text" name="collectivite_education_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="collectivite_education_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="collectivite_education_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->collectivite_education_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="collectivite_education_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Personnels Saisonniers -->
            <tr data-category="personnels-saisonniers">
                <td>
                    <i class="fas fa-calendar-alt text-warning me-2"></i>Personnels Saisonniers
                </td>
                <td><input type="text" name="personnels_saisonniers_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="personnels_saisonniers_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="personnels_saisonniers_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->personnels_saisonniers_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="personnels_saisonniers_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- EPN -->
            <tr data-category="epn">
                <td>
                    <i class="fas fa-building text-secondary me-2"></i>EPN
                </td>
                <td><input type="text" name="epn_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="epn_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="epn_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="epn_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->epn_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="epn_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="ced">
                <td>
                    <i class="fas fa-landmark text-primary me-2"></i>CED
                </td>
                <td><input type="text" name="ced_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="ced_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="ced_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="ced_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->ced_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="ced_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="ecom">
                <td>
                    <i class="fas fa-store text-success me-2"></i>ECOM
                </td>
                <td><input type="text" name="ecom_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="ecom_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="ecom_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="ecom_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->ecom_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="ecom_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <tr data-category="cfp-cpam">
                <td>
                    <i class="fas fa-user-graduate text-danger me-2"></i>CFP-CPAM
                </td>
                <td><input type="text" name="cfp_cpam_net" class="form-control net" placeholder="0"></td>
                <td><input type="text" name="cfp_cpam_revers" class="form-control revers" placeholder="0"></td>
                <td><input type="text" name="cfp_cpam_total_courant" class="form-control total_courant" readonly></td>
                <td><input type="text" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->cfp_cpam_total_courant ?? 0 }}" readonly></td>
                <td><input type="text" name="cfp_cpam_total_demande" class="form-control total_demande" readonly></td>
            </tr>

            <!-- Ligne de total automatique -->
            <tr class="total-row">
                <td>
                    <i class="fas fa-calculator me-2"></i><strong>TOTAL GÉNÉRAL</strong>
                </td>
                <td><input type="text" name="total_net" class="form-control" id="total_net" readonly></td>
                <td><input type="text" name="total_revers" class="form-control" id="total_revers" readonly></td>
                <td><input type="text" name="total_courant" class="form-control" id="total_courant" readonly></td>
                <td><input type="text" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
                <td><input type="text" name="total_demande" class="form-control" id="total_demande" readonly></td>
            </tr>
        </tbody>
    </table>
</div>
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

        document.querySelector('form').addEventListener('submit', function(e) {
            const numericFields = document.querySelectorAll('.net, .revers, .total_courant, .ancien_salaire, .total_demande, #total_net, #total_revers, #total_courant, #total_salaire_ancien, #total_demande, #montant_disponible, #solde');

            numericFields.forEach(field => {
                field.value = unformatNumber(field.value);
            });
        });

        calculateTotals();
    });
    </script>


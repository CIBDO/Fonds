import { NumberFormatter } from './number-formatter';

export class FormHandler {
    constructor() {
        this.setupEventListeners();
        this.initializeFields();
    }

    setupEventListeners() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }

        document.querySelectorAll('.net, .revers, .ancien_salaire')
            .forEach(input => {
                input.addEventListener('input', this.handleInput.bind(this));
            });
    }

    handleInput(event) {
        const input = event.target;
        const value = NumberFormatter.unformat(input.value);
        input.value = NumberFormatter.format(value);

        // Mettre à jour le champ caché correspondant
        const hiddenInput = document.querySelector(`input[type="hidden"][name="${input.dataset.name}"]`);
        if (hiddenInput) {
            hiddenInput.value = value;
        }

        this.calculateTotals();
    }

    handleSubmit(event) {
        this.updateAllHiddenFields();
    }

    updateAllHiddenFields() {
        document.querySelectorAll('.net, .revers, .ancien_salaire').forEach(input => {
            const hiddenInput = document.querySelector(`input[type="hidden"][name="${input.dataset.name}"]`);
            if (hiddenInput) {
                hiddenInput.value = NumberFormatter.unformat(input.value);
            }
        });

        // Mise à jour des totaux
        ['net', 'revers', 'courant', 'salaire_ancien', 'demande'].forEach(field => {
            const visibleInput = document.getElementById(`total_${field}`);
            const hiddenInput = document.querySelector(`input[name="total_${field}"]`);
            if (visibleInput && hiddenInput) {
                hiddenInput.value = NumberFormatter.unformat(visibleInput.value);
            }
        });
    }

    calculateTotals() {
        let totalNet = 0;
        let totalRevers = 0;
        let totalCourant = 0;
        let totalAncien = 0;

        document.querySelectorAll('.net').forEach((input, index) => {
            const net = NumberFormatter.unformat(input.value);
            const revers = NumberFormatter.unformat(
                document.querySelectorAll('.revers')[index].value
            );
            const ancien = NumberFormatter.unformat(
                document.querySelectorAll('.ancien_salaire')[index].value
            );

            totalNet += net;
            totalRevers += revers;
            totalCourant += (net + revers);
            totalAncien += ancien;

            // Mise à jour des totaux par ligne
            const totalCourantField = document.querySelectorAll('.total_courant')[index];
            const totalDemandeField = document.querySelectorAll('.total_demande')[index];

            totalCourantField.value = NumberFormatter.format(net + revers);
            totalDemandeField.value = NumberFormatter.format((net + revers) - ancien);
        });

        // Mise à jour des totaux généraux
        document.getElementById('total_net').value = NumberFormatter.format(totalNet);
        document.getElementById('total_revers').value = NumberFormatter.format(totalRevers);
        document.getElementById('total_courant').value = NumberFormatter.format(totalCourant);
        document.getElementById('total_salaire_ancien').value = NumberFormatter.format(totalAncien);
        document.getElementById('total_demande').value = NumberFormatter.format(totalCourant - totalAncien);

        this.updateAllHiddenFields();
    }

    initializeFields() {
        document.querySelectorAll('.net, .revers, .ancien_salaire').forEach(input => {
            if (input.value) {
                input.value = NumberFormatter.format(NumberFormatter.unformat(input.value));
            }
        });
        this.calculateTotals();
    }
}

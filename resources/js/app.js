import './bootstrap';

import Alpine from 'alpinejs';
import Cleave from 'cleave.js';
window.Alpine = Alpine;

Alpine.start();

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,  // La clé Pusher doit être définie dans votre fichier .env
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,  // Le cluster Pusher
    forceTLS: true
});

// Remplacer 'userId' par l'ID de l'utilisateur connecté.
let userId = document.querySelector("meta[name='user-id']").getAttribute('content');

Echo.private(`user.${userId}`)
    .notification((notification) => {
        // Mettez à jour l'interface utilisateur pour afficher la nouvelle notification
        console.log(notification);
    });

new Cleave('.net', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
});

new Cleave('.revers', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
});

new Cleave('.total_courant', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
});

new Cleave('.ancien_salaire', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
});

new Cleave('.total_demande', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand'
});
    
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'main-green': '#009739',
                'main-yellow': '#FFD600',
                'main-red': '#E30613',
                'main-black': '#000000',
                'main-white': '#FFFFFF',
            },
            fontFamily: {
                sans: ['JetBrains Mono', 'Montserrat', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

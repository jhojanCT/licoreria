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
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 4px 6px -1px rgb(15 23 42 / 0.05), 0 10px 24px -4px rgb(15 23 42 / 0.08)',
                'soft-lg': '0 8px 10px -2px rgb(15 23 42 / 0.06), 0 20px 40px -12px rgb(15 23 42 / 0.12)',
                glow: '0 0 50px -12px rgb(99 102 241 / 0.45)',
            },
        },
    },

    plugins: [forms],
};

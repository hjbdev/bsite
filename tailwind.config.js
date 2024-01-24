import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import prose from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Geo', ...defaultTheme.fontFamily.mono],
            },
            maxWidth: {
                '8xl': '90rem',
            },
            minHeight: ({ theme }) => ({
                ...theme('spacing')
            }),
            minWidth: ({ theme }) => ({
                ...theme('spacing')
            }),
        },
    },

    plugins: [forms, prose],
};

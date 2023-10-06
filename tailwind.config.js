import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './node_modules/@hjbdev/ui/src/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            minHeight: ({ theme }) => ({
                ...theme('spacing')
            }),
            minWidth: ({ theme }) => ({
                ...theme('spacing')
            }),
        },
    },

    plugins: [forms],
};

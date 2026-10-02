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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#17bebb',
                    50: '#e1fbf9',
                    100: '#b4f5f1',
                    200: '#81eeea',
                    300: '#4be7e2',
                    400: '#23dad5',
                    500: '#17bebb',
                    600: '#119b9a',
                    700: '#0c7a7a',
                    800: '#085e5e',
                    900: '#044647',
                },
                secondary: {
                    DEFAULT: '#FFB800',
                    50: '#FFF8E6',
                    100: '#FFF1CC',
                    200: '#FFE399',
                    300: '#FFD566',
                    400: '#FFC633',
                    500: '#FFB800',
                    600: '#CC9300',
                    700: '#996E00',
                    800: '#664A00',
                    900: '#332500',
                },
            },
        },
    },

    plugins: [forms],
};

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
                brand: {
                    50: '#FFF1F5',
                    100: '#FFE1EA',
                    200: '#FFB8CB',
                    300: '#FF90AC',
                    400: '#FF678D',
                    500: '#FF3E6E',
                    600: '#FF1254',
                    700: '#DB003C',
                    800: '#B0002F',
                    900: '#7A001F',
                    950: '#4B0013',
                    DEFAULT: '#FF1254',
                },
                indigo: {
                    50: '#FFF1F5',
                    100: '#FFE1EA',
                    200: '#FFB8CB',
                    300: '#FF90AC',
                    400: '#FF678D',
                    500: '#FF3E6E',
                    600: '#FF1254',
                    700: '#DB003C',
                    800: '#B0002F',
                    900: '#7A001F',
                    950: '#4B0013',
                },
            },
        },
    },

    plugins: [forms],
};

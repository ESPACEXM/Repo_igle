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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', 'Merriweather', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                church: {
                    50: '#f0f6ff',
                    100: '#e0edff',
                    200: '#b8d4ff',
                    300: '#7ab8ff',
                    400: '#3696ff',
                    500: '#0c75e6',
                    600: '#005bd4',
                    700: '#0048ab',
                    800: '#063d8c',
                    900: '#0a3574',
                    950: '#07214a',
                },
                gold: {
                    50: '#fdf9ed',
                    100: '#f9eec9',
                    200: '#f3db8e',
                    300: '#ecc253',
                    400: '#e6ad30',
                    500: '#df8f19',
                    600: '#c46c11',
                    700: '#a34b11',
                    800: '#853a15',
                    900: '#6d3015',
                    950: '#3f1708',
                },
                cream: {
                    50: '#fdfcf8',
                    100: '#faf7ed',
                    200: '#f3edd6',
                    300: '#ebe0b8',
                },
            },
            backgroundImage: {
                'church-gradient': 'linear-gradient(135deg, #07214a 0%, #0a3574 50%, #0048ab 100%)',
                'church-gradient-light': 'linear-gradient(135deg, #f0f6ff 0%, #fdf9ed 100%)',
                'golden-gradient': 'linear-gradient(135deg, #f9eec9 0%, #ecc253 50%, #e6ad30 100%)',
            },
            boxShadow: {
                'church': '0 4px 20px -2px rgba(7, 33, 74, 0.15)',
                'church-lg': '0 10px 40px -4px rgba(7, 33, 74, 0.2)',
                'gold': '0 4px 20px -2px rgba(223, 143, 25, 0.2)',
            },
        },
    },

    plugins: [forms],
};

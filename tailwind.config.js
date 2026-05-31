import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#5645d4',
                    pressed: '#4534b3',
                    deep: '#3a2a99',
                },
                navy: {
                    DEFAULT: '#0a1530',
                    deep: '#070f24',
                    mid: '#1a2a52',
                },
                ink: {
                    DEFAULT: '#1a1a1a',
                    deep: '#000000',
                },
                charcoal: '#37352f',
                slate: '#5d5b54',
                steel: '#787671',
                stone: '#a4a097',
                muted: '#bbb8b1',
                surface: {
                    DEFAULT: '#f6f5f4',
                    soft: '#fafaf9',
                },
                hairline: {
                    DEFAULT: '#e5e3df',
                    soft: '#ede9e4',
                },
            },
            borderRadius: {
                btn: '8px',
                card: '12px',
            },
        },
    },

    plugins: [forms, typography],
};

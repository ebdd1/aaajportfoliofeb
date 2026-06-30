import defaultTheme from 'tailwindcss/defaultTheme';

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
            colors: {
                cream: '#FAF8F1',
                oat: '#F5F1E8',
                'oat-dark': '#E8E2D3',
                paper: '#FFFFFF',
                terracotta: {
                    DEFAULT: '#C96442',
                    dark: '#B5532F',
                    light: '#E0916F',
                },
                ink: '#3D3929',
                taupe: '#6B6456',
                'taupe-light': '#9C9484',
                // ElevenLabs inspired - monochrome
                'midnight-ink': '#000000',
                'ash-border': '#e5e5e5',
                'warm-sand': '#f5f3f1',
                'driftwood': '#777169',
                'fog': '#a59f97',
                'parchment-white': '#fdfcfc',
            },
            fontFamily: {
                serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                'elevated': '0 0px 1px 0px rgba(0,0,0,0.4), 0 1px 1px 0px rgba(0,0,0,0.04), 0 2px 4px 0px rgba(0,0,0,0.04)',
                'inset': 'inset 0 0px 0px 0.5px rgba(0,0,0,0.075)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}

import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans:    ['DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Syne', 'sans-serif'],
                mono:    ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                slate: {
                    925: '#0f1729',
                    950: '#080f1e',
                },
            },
            animation: {
                'fade-in':     'fadeIn 0.3s ease-out',
                'slide-up':    'slideUp 0.3s ease-out',
                'pulse-slow':  'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn:  { from: { opacity: '0' },                 to: { opacity: '1' } },
                slideUp: { from: { transform: 'translateY(8px)', opacity: '0' }, to: { transform: 'translateY(0)', opacity: '1' } },
            },
        },
    },
    plugins: [],
};

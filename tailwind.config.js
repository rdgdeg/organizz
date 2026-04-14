import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                /** Orange chaleureux (type Spectra) — actions & accents */
                ember: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#ff6b35',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 2px 15px -3px rgb(30 58 138 / 0.08), 0 10px 25px -5px rgb(30 58 138 / 0.06)',
                card: '0 1px 3px rgb(15 23 42 / 0.06), 0 8px 24px -4px rgb(15 23 42 / 0.08)',
                glow: '0 0 40px -10px rgb(37 99 235 / 0.45)',
                elevated:
                    '0 10px 15px -3px rgb(15 23 42 / 0.06), 0 4px 6px -2px rgb(15 23 42 / 0.04), 0 0 0 1px rgb(15 23 42 / 0.04)',
                'ember-glow': '0 12px 32px -8px rgb(255 107 53 / 0.35)',
            },
            backgroundImage: {
                mesh: 'radial-gradient(at 40% 20%, rgb(59 130 246 / 0.18) 0px, transparent 50%), radial-gradient(at 80% 0%, rgb(37 99 235 / 0.15) 0px, transparent 50%), radial-gradient(at 0% 60%, rgb(96 165 250 / 0.12) 0px, transparent 45%)',
                'hero-gradient':
                    'linear-gradient(135deg, rgb(15 23 42) 0%, rgb(30 58 138) 40%, rgb(37 99 235) 100%)',
                /** Fond espace organisateur : bleu + touches orange */
                'organizer-mesh':
                    'radial-gradient(ellipse 100% 80% at 100% -20%, rgb(255 107 53 / 0.11) 0%, transparent 55%), radial-gradient(ellipse 70% 60% at 0% 40%, rgb(37 99 235 / 0.09) 0%, transparent 50%), radial-gradient(ellipse 50% 40% at 80% 100%, rgb(59 130 246 / 0.08) 0%, transparent 45%)',
                'card-grid-fade':
                    'linear-gradient(to top, rgb(248 250 252) 0%, transparent 100%), repeating-linear-gradient(0deg, rgb(226 232 240 / 0.35) 0px, rgb(226 232 240 / 0.35) 1px, transparent 1px, transparent 20px), repeating-linear-gradient(90deg, rgb(226 232 240 / 0.35) 0px, rgb(226 232 240 / 0.35) 1px, transparent 1px, transparent 20px)',
            },
            animation: {
                float: 'float 6s ease-in-out infinite',
                'float-delayed': 'float 7s ease-in-out infinite 1s',
                'pulse-soft': 'pulse-soft 4s ease-in-out infinite',
                shimmer: 'shimmer 8s linear infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-12px)' },
                },
                'pulse-soft': {
                    '0%, 100%': { opacity: '0.4' },
                    '50%': { opacity: '0.8' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '200% 0' },
                    '100%': { backgroundPosition: '-200% 0' },
                },
            },
        },
    },

    plugins: [forms],
};

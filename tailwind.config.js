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
                /**
                 * Escala neutral restaurada para que las utilidades bg-neutral-* y border-neutral-*
                 * vuelvan a producir fondos y bordes oscuros en modo dark como antes.
                 *
                 * Nota:
                 *  - Mantenemos algunos niveles (300–500) enlazados a variables de texto para conservar
                 *    la personalización accesible.
                 *  - Niveles altos (700–900) ahora son valores hex explícitos oscuros, evitando que
                 *    bg-neutral-800/900 se conviertan en texto blanco (rotura reportada).
                 *  - Si se necesita tematizar estos hex en modo claro, puede añadirse un plugin o
                 *    clases específicas; para el objetivo inmediato (restaurar apariencia dark) es suficiente.
                 */
                neutral: {
                    50:  '#fafafa',          // (no se usa en dark normalmente)
                    100: '#f5f5f5',          // text-neutral-100 -> texto casi blanco en dark (inputs)
                    200: '#e5e5e5',          // text-neutral-200
                    300: 'var(--color-text-secondary)', // gris medio (#999) dinámico
                    400: 'var(--color-text-muted)',     // gris apagado (#666) dinámico
                    500: 'var(--color-text-primary)',   // blanco
                    600: '#d9d9d9',          // tono claro adicional
                    700: '#333333',          // bordes / separadores oscuros
                    800: '#222222',          // fondos de paneles (coincide con --color-bg-secondary)
                    900: '#181818',          // fondo principal (coincide con --color-bg-primary)
                },
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    hover: 'var(--color-accent-hover)',
                    active: 'var(--color-accent-active)',
                }
            }
        },
    },

    plugins: [forms],
};

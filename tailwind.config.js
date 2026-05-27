/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                serif: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
            },
            colors: {
                accent: {
                    DEFAULT: '#ea580c',  // orange-600
                    light: '#fff7ed',    // orange-50
                    dark: '#fb923c',     // orange-400
                },
            },
        },
    },
    plugins: [require('@tailwindcss/typography')],
};

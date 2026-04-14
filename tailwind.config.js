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
                    DEFAULT: '#4f46e5',  // indigo-600
                    light: '#eef2ff',    // indigo-50
                    dark: '#818cf8',     // indigo-400
                },
            },
        },
    },
    plugins: [require('@tailwindcss/typography')],
};

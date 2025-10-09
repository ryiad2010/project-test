import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './app/Filament/**/*.php',        // Include Filament PHP files
    './app/Providers/Filament/*.php',  // Include panel provider
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          50:  '#eff6ff',
          100: '#dbeafe',
          200: '#6ee7b7',
          500: '#3b82f6',
          700: '#1e40af',
        },
          accent: {
          50:   '#d1fae5',
          100:  '#a7f3d0',
          200:  '#6ee7b7', // ← define 200
          500:  '#10b981',
          700:  '#059669',
          DEFAULT: '#10b981',
        },
      },
    },
  },
  plugins: [
    // e.g. require('@tailwindcss/forms'),
  ],
};

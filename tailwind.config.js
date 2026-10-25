import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // <- necesario para Filament/Tailwind en modo oscuro

  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',

    // Vistas Blade y Livewire/Volt
    './resources/views/**/*.blade.php',
    './resources/views/livewire/*.blade.php',
    './resources/**/*.volt.php',

    // Filament
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],

  safelist: [
    // Clases oscuras que podrías usar dinámicamente en el modal
    'bg-black',
    'bg-black/70',
    'bg-gray-900',
    'bg-gray-800',
    'text-white',
    'border-gray-700',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [forms, typography],
}

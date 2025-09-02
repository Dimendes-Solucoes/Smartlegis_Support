import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
    './resources/js/**/*.ts',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  safelist: [
    {
      pattern: /bg-(indigo|blue|red|yellow|orange|green|gray|pink)-500/,
    },
    {
      pattern: /border-(indigo|blue|red|yellow|orange|green|gray|pink)-600/,
    },
    {
      pattern: /hover:bg-(indigo|blue|red|yellow|orange|green|gray|pink)-700/,
    },
    {
      pattern: /active:bg-(indigo|blue|red|yellow|orange|green|gray|pink)-900/,
    },
    {
      pattern: /focus:border-(indigo|blue|red|yellow|orange|green|gray|pink)-900/,
    },
    {
      pattern: /focus:ring-(indigo|blue|red|yellow|orange|green|gray|pink)-300/,
    },
  ],

  darkMode: 'class',

  plugins: [forms],
};

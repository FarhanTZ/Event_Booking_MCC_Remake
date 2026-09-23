/** @type {import('tailwindcss').Config} */
import forms from '@tailwindcss/forms';

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/View/Components/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        mcc: {
          primary: {
            DEFAULT: '#001A72', // Official Brand Blue
            hover: '#001250',
            light: '#EEF2FF',
            50: '#F0F3FF',
            100: '#E0E7FF',
            200: '#C7D2FE',
            300: '#A5B4FC',
            400: '#818CF8',
            500: '#6366F1',
            600: '#001A72', // Primary Anchor
            700: '#00155C',
            800: '#001046',
            900: '#000A30',
          },
        },
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.05)',
        'elevated': '0 10px 15px -3px rgb(0 26 114 / 0.08), 0 4px 6px -4px rgb(0 26 114 / 0.04)',
      },
      borderRadius: {
        'brand': '0.625rem', // 10px - standar elemen MCC
      }
    },
  },
  plugins: [
    forms,
  ],
};
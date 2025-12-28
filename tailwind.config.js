/** @type {import('tailwindcss').Config} */
module.exports = {
  // Prefix Tailwind classes to avoid Bootstrap conflicts
  prefix: 'tw-',
  
  // Disable important by default to allow easy overrides
  important: false,
  
  // Scan all PHP and HTML files for Tailwind classes
  content: [
    './app/**/*.php',
    './app/views/**/*.php',
    './app/modules/**/*.php',
    './themes/**/*.php',
    './themes/**/*.html',
    './index.php',
    './*.php',
  ],
  
  // Extend theme with custom colors matching the existing design
  theme: {
    extend: {
      colors: {
        // Primary brand colors
        'primary': '#467fcf',
        'primary-dark': '#3a6fb0',
        'primary-light': '#5d92e0',
        'secondary': '#868e96',
        
        // Status colors
        'success': '#5eba00',
        'success-dark': '#4a9600',
        'info': '#45aaf2',
        'warning': '#f1c40f',
        'danger': '#cd201f',
        
        // Extended palette
        'azure': '#45aaf2',
        'teal': '#2bcbba',
        'cyan': '#17a2b8',
        'orange': '#fd9644',
        'purple': '#a55eea',
        'pink': '#f66d9b',
        
        // Background colors
        'body-bg': '#d1e8ff',
        'card-bg': '#ffffff',
      },
      
      // Custom shadows matching modern design
      boxShadow: {
        'xs': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'card': '0 6px 18px rgba(26, 46, 85, 0.08)',
        'card-hover': '0 12px 28px rgba(26, 46, 85, 0.15)',
        'card-active': '0 3px 12px rgba(26, 46, 85, 0.12)',
      },
      
      // Modern spacing scale
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
      },
      
      // Custom border radius
      borderRadius: {
        'card': '0.75rem',
      },
      
      // Custom font families
      fontFamily: {
        sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif'],
      },
      
      // Custom animations
      animation: {
        'fade-in': 'fadeIn 0.3s ease-in-out',
        'slide-in': 'slideIn 0.3s ease-out',
        'bounce-slow': 'bounce 2s infinite',
      },
      
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideIn: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
      },
    },
  },
  
  // Dark mode support
  darkMode: 'class',
  
  plugins: [],
}


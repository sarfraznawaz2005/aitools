/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./storage/framework/views/*.php",
        "node_modules/preline/dist/*.js",
    ],
    theme: {
        fontFamily: {
            sans: ['Ubuntu', 'sans-serif'],
        },
        extend: {
            colors: {
                // 'dark-text': '#353f55',
                // 'neutral-900': '#131B28',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('tailwindcss-animated'),
        require('preline/plugin'),
    ],
}

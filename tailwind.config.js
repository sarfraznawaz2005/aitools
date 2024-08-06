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
    safelist: [
        'from-blue-400',
        'to-blue-600',
        'from-green-400',
        'to-green-600',
        'from-yellow-400',
        'to-yellow-600',
        'from-gray-400',
        'to-gray-600',
        'size-16',
    ],
    theme: {
        fontFamily: {
            sans: ['Nunito', 'sans-serif'],
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

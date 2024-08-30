/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/**/*.css",
        "./storage/framework/views/*.php",
        "node_modules/preline/dist/*.js",
    ],
    safelist: [
        'w-48',
        'w-64',
        'list-disc',
        'list-decimal',
        'list-square',
        'list-circle',
        'list-lower-alpha',
        'list-lower-roman',
        'list-upper-alpha',
        'list-upper-roman',
        'text-gray-600',
        'from-blue-400',
        'to-blue-600',
        'from-green-400',
        'to-green-600',
        'from-yellow-400',
        'to-yellow-600',
        'from-gray-400',
        'to-gray-600',
        'size-16',
        'bg-blue-500',
        'bg-green-500',
        'bg-yellow-500',
        'bg-red-500',
        'marker:text-blue-600',
        'xxxxx',
        'text-red-600',
        'text-blue-600',
        'text-green-600',
        'text-purple-600',
        'text-gray-600',
        'text-pink-600',
        'text-yellow-600',
    ],
    theme: {
        fontFamily: {
            sans: ['sans-serif'],
        },
        extend: {
            utilities: {
                '.word-break-all': {
                    'word-break': 'break-word',
                    'overflow-wrap': 'break-word'
                },
                '.scrollbar-code': {
                    '&::-webkit-scrollbar': {
                        height: '8px',
                    },
                    '&::-webkit-scrollbar-thumb': {
                        backgroundColor: 'rgba(156, 163, 175, 0.5)',
                        borderRadius: '4px',
                    },
                },
                '.break-long-words': {
                    'word-break': 'break-word',
                    'overflow-wrap': 'break-word',
                    'hyphens': 'auto',
                },
            },
            colors: {
                // 'dark-text': '#353f55',
                // 'neutral-900': '#131B28',
            },
            listStyleType: {
                none: 'none',
                disc: 'disc',
                decimal: 'decimal',
                square: 'square',
                circle: 'circle',
                'lower-alpha': 'lower-alpha',
                'lower-roman': 'lower-roman',
                'upper-alpha': 'upper-alpha',
                'upper-roman': 'upper-roman',
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('tailwindcss-animated'),
        require('preline/plugin'),
    ],
}

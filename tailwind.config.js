export default {
    content: [
        './app/views/**/*.php', // all php files containing Tailwind classes
    ],
    theme: {
        extend: {
            keyframes: {
                fadein: {
                    'from': {bottom: '0', opacity: '0'},
                    'to': {bottom: '30px', opacity: '1'}
                },
                fadeout: {
                    'from': {bottom: '30px', opacity: '1'},
                    'to': {bottom: '0', opacity: '0'}
                }
            }
        }
    },
    plugins: []
}
/** @type {import('tailwindcss').Config} */
export default {
    prefix: 'tw-',
    // separator: '_',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    mode: 'jit',
    theme: {
        screens: {
            'xss': '400px',
            'xs': '480px',
            'sm': '576px',
            'md': '768px',
            'lg': '992px',
            'xl': '1200px',
            'xxl': '1400px',
            'xxxl': '1600px',
            'fhd': '1920px',
            '2k': '2880px',
            '4k': '3840px',
        },
        container: {
            center: true,
            padding: {
                DEFAULT: '15px',
                md: '30px',
            },
        },
        gap: {
            'col': '12px',
            'columns': '24px'
        },
        padding: {
            'row': '100px',
        },
        extend: {
            maxWidth: {
                screen: {
                    'xs': '420px',
                    'sm': '540px',
                    'md': '720px',
                    'lg': '960px',
                    'xl': '1140px',
                    'xxl': '1320px',
                    'xxxl': '1540px',
                    'fhd': '1820px',
                    '2k': '2760px',
                    '4k': '3600px',
                }
            },
        },
    },

    plugins: [
        // require("@tailwindcss/forms"),
        // require("@tailwindcss/typography")
    ],
};
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./templates/**/*.html.twig",
        "./assets/js/*.js",
        "./src/**/*.php",
    ],
    theme: {
        extend: {},
        container: {
            center: true,
        },
    },
    plugins: [],
};

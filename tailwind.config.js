/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                rhino: "#253c4e",
                porcelain: "#eef1f2",
            },
        },
    },
    plugins: [],
};

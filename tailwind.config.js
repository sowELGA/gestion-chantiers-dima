import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    theme: {
        extend: {
            colors: {
                primary: "var(--color-primary)",
                secondary: "var(--color-secondary)",
                accent: "var(--color-accent)",
                lightbg: "var(--color-lightbg)",
                muted: "var(--color-muted)",
            },
        },
    },
    plugins: [],
};

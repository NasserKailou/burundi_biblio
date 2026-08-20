import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                heading: ['Lexend', ...defaultTheme.fontFamily.sans],
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                bns: {
                    primary: 'var(--color-primary)',
                    'on-primary': 'var(--color-on-primary)',
                    accent: 'var(--color-accent)',
                    'on-accent': 'var(--color-on-accent)',
                    background: 'var(--color-background)',
                    foreground: 'var(--color-foreground)',
                    card: 'var(--color-card)',
                    muted: 'var(--color-muted)',
                    'muted-foreground': 'var(--color-muted-foreground)',
                    border: 'var(--color-border)',
                    success: 'var(--color-success)',
                    destructive: 'var(--color-destructive)',
                    ring: 'var(--color-ring)',
                    reader: 'var(--color-reader-bg)',
                    'reader-toolbar': 'var(--color-reader-toolbar)',
                },
            },
        },
    },
    plugins: [],
};

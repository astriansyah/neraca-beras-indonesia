import katex from 'katex';

/**
 * Entry JS untuk halaman Metodologi & Kualitas Data (/metodologi)
 * Merender seluruh formula matematika menggunakan KaTeX
 */
document.addEventListener('DOMContentLoaded', () => {
    const mathElements = document.querySelectorAll('[data-katex]');

    mathElements.forEach((el) => {
        const tex = el.getAttribute('data-katex');
        const isDisplayMode = el.getAttribute('data-display') === 'block';

        if (!tex) return;

        try {
            katex.render(tex, el, {
                displayMode: isDisplayMode,
                throwOnError: false,
                output: 'htmlAndMathml',
            });
        } catch (error) {
            console.error('KaTeX rendering error for:', tex, error);
            el.textContent = tex;
        }
    });
});

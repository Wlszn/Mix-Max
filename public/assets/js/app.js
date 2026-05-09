window.addEventListener('load', () => {
    const loader = document.getElementById('page-loader');

    if (!loader) return;

    setTimeout(() => {
        loader.classList.add('loader-hidden');
    }, 250);
});
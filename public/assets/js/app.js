function hidePageLoader() {
    const loader = document.getElementById('page-loader');

    if (!loader) return;

    loader.classList.add('loader-hidden');

    setTimeout(() => {
        loader.style.display = 'none';
    }, 400);
}

window.addEventListener('load', () => {
    setTimeout(hidePageLoader, 250);
});

setTimeout(hidePageLoader, 3000);
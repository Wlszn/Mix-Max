document.addEventListener('DOMContentLoaded', () => {
    const filterToggle = document.getElementById('filterToggle');
    const filterPanel = document.getElementById('filterPanel');

    if (!filterToggle || !filterPanel) return;

    filterToggle.addEventListener('click', () => {
        const isClosed = filterPanel.classList.contains('max-h-0');

        if (isClosed) {
            filterPanel.classList.remove('max-h-0', 'opacity-0', 'p-0', 'border-transparent');
            filterPanel.classList.add('max-h-96', 'opacity-100', 'p-5');
        } else {
            filterPanel.classList.remove('max-h-96', 'opacity-100', 'p-5');
            filterPanel.classList.add('max-h-0', 'opacity-0', 'p-0', 'border-transparent');
        }
    });
});
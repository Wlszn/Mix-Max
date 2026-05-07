const basePath = window.basePath;

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('live-search-input');
    const resultsBox = document.getElementById('live-search-results');
    const clearButton = document.getElementById('clear-live-search');

    if (!input || !resultsBox || !clearButton) return;

    let searchTimer = null;

    function hideResults() {
        resultsBox.classList.add('hidden');
        resultsBox.innerHTML = '';
    }

    function showLoading() {
        resultsBox.classList.remove('hidden');
        resultsBox.innerHTML = `
            <div class="px-4 py-3 text-sm text-slate-500">
                Searching...
            </div>
        `;
    }

    function renderResults(events) {
        if (!events.length) {
            resultsBox.classList.remove('hidden');
            resultsBox.innerHTML = `
                <div class="px-4 py-3 text-sm text-slate-500">
                    No events found
                </div>
            `;
            return;
        }

        resultsBox.classList.remove('hidden');

        resultsBox.innerHTML = events.map(event => {
            const image = event.imageUrl
                ? event.imageUrl
                : 'https://via.placeholder.com/80x80?text=Event';

            const title = escapeHtml(event.title ?? 'Untitled Event');
            const artist = escapeHtml(event.artist ?? '');
            const venue = escapeHtml(event.venueName ?? '');
            const city = escapeHtml(event.city ?? '');
            const date = escapeHtml(event.date ?? '');

            return `
                <a href="${basePath}/events/${event.eventId}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0">
                    <img src="${image}"
                         alt="${title}"
                         class="w-12 h-12 rounded-lg object-cover bg-slate-200">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900 truncate">${title}</p>
                        <p class="text-xs text-slate-500 truncate">${artist}</p>
                        <p class="text-xs text-slate-500 truncate">${venue}${city ? ', ' + city : ''}</p>
                    </div>
                    <div class="text-xs text-blue-600 font-medium whitespace-nowrap">
                        ${date}
                    </div>
                </a>
            `;
        }).join('');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    input.addEventListener('input', () => {
        const query = input.value.trim();

        clearButton.classList.toggle('hidden', query.length === 0);

        if (searchTimer) {
            clearTimeout(searchTimer);
        }

        if (query.length < 2) {
            hideResults();
            return;
        }

        searchTimer = setTimeout(async () => {
            showLoading();

            try {
                const response = await fetch(`${basePath}/events/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                renderResults(data.events ?? []);
            } catch (error) {
                resultsBox.classList.remove('hidden');
                resultsBox.innerHTML = `
                    <div class="px-4 py-3 text-sm text-red-600">
                        Search failed. Please try again.
                    </div>
                `;
            }
        }, 300);
    });

    clearButton.addEventListener('click', () => {
        input.value = '';
        clearButton.classList.add('hidden');
        hideResults();
        input.focus();
    });

    document.addEventListener('click', (event) => {
        const wrapper = document.getElementById('live-search-wrapper');

        if (wrapper && !wrapper.contains(event.target)) {
            hideResults();
        }
    });
});

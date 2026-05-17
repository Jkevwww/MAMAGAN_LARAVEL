import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const showPageLoader = () => {
    document.documentElement.classList.add('is-navigating');
};

window.showPageLoader = showPageLoader;

window.addEventListener('pageshow', () => {
    document.documentElement.classList.remove('is-navigating');
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');

    if (!link) {
        return;
    }

    const url = new URL(link.href, window.location.href);
    const isModifiedClick = event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
    const isExternal = url.origin !== window.location.origin;
    const isSamePageAnchor = url.pathname === window.location.pathname && url.hash;
    const skipsLoader = link.target === '_blank' || link.hasAttribute('download') || link.dataset.noLoader === 'true';

    if (!isModifiedClick && !isExternal && !isSamePageAnchor && !skipsLoader) {
        showPageLoader();
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || form.dataset.noLoader === 'true') {
        return;
    }

    showPageLoader();
});

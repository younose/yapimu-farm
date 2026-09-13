(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    let deferredPrompt = null;

    function getButton() {
        return document.getElementById('pwa-install-btn');
    }

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function showButton() {
        const btn = getButton();

        if (btn && !isStandalone() && !sessionStorage.getItem('pwaInstallDismissed')) {
            btn.classList.add('show');
        }
    }

    function hideButton() {
        const btn = getButton();

        if (btn) {
            btn.classList.remove('show');
        }
    }

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        showButton();
    });

    window.addEventListener('appinstalled', function () {
        deferredPrompt = null;
        hideButton();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const btn = getButton();

        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            if (!deferredPrompt) {
                return;
            }

            hideButton();
            deferredPrompt.prompt();
            deferredPrompt.userChoice.finally(function () {
                deferredPrompt = null;
            });
        });

        const closeBtn = document.getElementById('pwa-install-close');

        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sessionStorage.setItem('pwaInstallDismissed', '1');
                hideButton();
            });
        }
    });
})();

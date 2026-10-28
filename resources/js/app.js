import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const APP_VERSION = document.querySelector('meta[name="app-version"]')?.content ?? 'dev';
const APP_BUILD = document.querySelector('meta[name="app-build"]')?.content ?? 'dev';
const UPDATE_ATTEMPT_KEY = 'pwa-update-attempt';
const UPDATE_RETRY_MS = 5 * 60 * 1000;
const CHECK_INTERVAL_MS = 30 * 60 * 1000;

let isReloading = false;
let lastCheckedAt = 0;

const notify = (name, detail = {}) => {
    window.dispatchEvent(new CustomEvent(name, { detail }));
};

const reloadPage = () => {
    if (isReloading) return;
    isReloading = true;
    window.location.reload();
};

const readAttempt = () => {
    try {
        const raw = sessionStorage.getItem(UPDATE_ATTEMPT_KEY);

        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
};

const writeAttempt = (build) => {
    try {
        sessionStorage.setItem(UPDATE_ATTEMPT_KEY, JSON.stringify({
            build,
            at: Date.now(),
        }));
    } catch {
        // Storage bisa tidak tersedia (mode privat); abaikan.
    }
};

const isVersionLower = (current, minimum) => {
    if (!minimum) return false;

    const parse = (value) => String(value).split('.').map((part) => parseInt(part, 10) || 0);
    const currentParts = parse(current);
    const minimumParts = parse(minimum);

    for (let index = 0; index < Math.max(currentParts.length, minimumParts.length); index++) {
        const left = currentParts[index] ?? 0;
        const right = minimumParts[index] ?? 0;

        if (left !== right) return left < right;
    }

    return false;
};

const checkVersion = async (registration) => {
    let payload;

    try {
        const response = await fetch('/version.json', {
            cache: 'no-store',
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) return;

        payload = await response.json();
    } catch {
        // Offline atau server bermasalah: jangan ganggu pengguna.
        return;
    }

    // Versi di bawah minimum: wajib update sebelum aplikasi bisa dipakai.
    if (payload.min_version && isVersionLower(APP_VERSION, payload.min_version)) {
        const targetBuild = payload.build ?? payload.version;
        const attempt = readAttempt();
        const recentlyAttempted = attempt?.build === targetBuild
            && Date.now() - attempt.at < UPDATE_RETRY_MS;

        notify('pwa:update-required', { version: payload.version, build: targetBuild });

        if (recentlyAttempted) {
            // Reload sudah dicoba tapi versi masih lama (mis. iOS/standalone).
            notify('pwa:update-stalled');
            return;
        }

        writeAttempt(targetBuild);
        registration?.update().catch(() => {});
        window.setTimeout(reloadPage, 1500);

        return;
    }

    // Build berubah: tawarkan reload supaya user memakai versi terbaru.
    if (APP_BUILD !== 'dev' && payload.build && payload.build !== APP_BUILD) {
        notify('pwa:update-available', { version: payload.version, build: payload.build });
    }
};

const registerServiceWorker = async () => {
    if (!('serviceWorker' in navigator)) return null;

    const hadController = Boolean(navigator.serviceWorker.controller);

    // SW baru mengambil alih: reload sekali agar halaman ikut versi baru.
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (hadController) reloadPage();
    });

    try {
        return await navigator.serviceWorker.register(`/sw.js?v=${encodeURIComponent(APP_BUILD)}`, {
            updateViaCache: 'none',
        });
    } catch (error) {
        console.log('SW registration failed: ', error);
        return null;
    }
};

window.addEventListener('load', async () => {
    const registration = await registerServiceWorker();

    await checkVersion(registration);

    const revalidate = () => {
        if (Date.now() - lastCheckedAt < 60 * 1000) return;

        lastCheckedAt = Date.now();
        registration?.update().catch(() => {});
        checkVersion(registration);
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') revalidate();
    });

    window.setInterval(revalidate, CHECK_INTERVAL_MS);
});

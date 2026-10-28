<div id="pwa-update-guard" style="display: none;"
    class="fixed inset-0 z-[100] items-center justify-center bg-gray-900/60 px-4 backdrop-blur-sm"
    role="dialog" aria-modal="true" aria-labelledby="pwa-update-title" aria-describedby="pwa-update-message">
    <div class="w-full max-w-sm rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div>
                <h2 id="pwa-update-title" class="text-base font-bold text-gray-900">Versi Baru Tersedia</h2>
                <p id="pwa-update-message" class="mt-1 text-sm text-gray-600">
                    Aplikasi telah diperbarui. Muat ulang untuk memakai versi terbaru.
                </p>
            </div>
        </div>

        <p id="pwa-update-hint"
            class="mt-4 hidden rounded-xl border border-amber-100 bg-amber-50 p-3 text-sm text-amber-800">
            Halaman belum berubah. Tutup lalu buka kembali aplikasi. Pada iOS, hapus aplikasi dari Home Screen lalu
            install ulang.
        </p>

        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
            <button type="button" id="pwa-update-dismiss"
                class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100">
                Nanti
            </button>
            <button type="button" id="pwa-update-action"
                class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-500/30 transition hover:bg-primary-700 active:scale-95">
                Muat Ulang Sekarang
            </button>
        </div>
    </div>
</div>

<script>
    (() => {
        const guard = document.getElementById('pwa-update-guard');
        if (!guard) return;

        const title = document.getElementById('pwa-update-title');
        const message = document.getElementById('pwa-update-message');
        const hint = document.getElementById('pwa-update-hint');
        const action = document.getElementById('pwa-update-action');
        const dismiss = document.getElementById('pwa-update-dismiss');

        const show = (required) => {
            title.textContent = required ? 'Aplikasi Perlu Diperbarui' : 'Versi Baru Tersedia';
            message.textContent = required ?
                'Versi aplikasi yang Anda gunakan sudah tidak didukung. Perbarui sekarang untuk melanjutkan.' :
                'Aplikasi telah diperbarui. Muat ulang untuk memakai versi terbaru.';

            hint.classList.toggle('hidden', !required);
            dismiss.classList.toggle('hidden', required);

            guard.style.display = 'flex';
            action.focus();
        };

        const hide = () => {
            guard.style.display = 'none';
        };

        window.addEventListener('pwa:update-available', () => show(false));
        window.addEventListener('pwa:update-required', () => show(true));
        window.addEventListener('pwa:update-stalled', () => {
            show(true);
            hint.classList.remove('hidden');
        });

        dismiss.addEventListener('click', hide);

        action.addEventListener('click', () => {
            action.disabled = true;
            action.textContent = 'Memuat ulang...';
            window.location.reload();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !dismiss.classList.contains('hidden')) hide();
        });
    })();
</script>

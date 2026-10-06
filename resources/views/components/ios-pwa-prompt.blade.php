<div x-data="iosPwaPrompt()" x-show="show" style="display: none;" class="fixed bottom-0 left-0 right-0 p-4 z-50 transition-transform duration-300 transform translate-y-0">
    <div class="bg-white rounded-2xl shadow-[0_-4px_20px_rgba(0,0,0,0.15)] p-4 flex flex-col gap-3 border border-gray-100">
        <div class="flex justify-between items-start">
            <div class="flex gap-3">
                <img src="{{ asset('images/logo.png') }}" class="w-12 h-12 rounded-xl object-contain bg-gray-50 p-1 border border-gray-100" alt="App Icon">
                <div>
                    <h3 class="font-bold text-gray-900 leading-tight text-base">Install SIM Pimpinan</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Akses aplikasi lebih cepat & mudah</p>
                </div>
            </div>
            <button @click="dismiss()" class="text-gray-400 hover:text-gray-600 p-1 -mt-1 -mr-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="bg-primary-50/50 rounded-xl p-3 text-sm text-gray-700 flex items-center justify-center gap-2 border border-primary-100/50">
            <span>Tap <svg class="inline-block w-5 h-5 text-primary-500 mx-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg> lalu pilih <strong>Add to Home Screen</strong></span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('iosPwaPrompt', () => ({
            show: false,
            init() {
                // Cek apakah device adalah iOS
                const isIos = () => {
                    const userAgent = window.navigator.userAgent.toLowerCase();
                    return /iphone|ipad|ipod/.test(userAgent);
                };
                
                // Cek apakah aplikasi sudah berjalan dalam mode standalone (PWA terinstall)
                const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);
                
                // Cek apakah user pernah men-dismiss banner ini
                const hasPromptBeenDismissed = localStorage.getItem('iosPwaPromptDismissed');

                if (isIos() && !isInStandaloneMode() && !hasPromptBeenDismissed) {
                    setTimeout(() => {
                        this.show = true;
                    }, 2000); // Tampilkan popup setelah 2 detik
                }
            },
            dismiss() {
                this.show = false;
                localStorage.setItem('iosPwaPromptDismissed', 'true');
            }
        }));
    });
</script>

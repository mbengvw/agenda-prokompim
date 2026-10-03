<x-app-layout>
    <!-- No Header for Dashboard -->

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Card -->
            <div class="bg-gradient-to-br from-primary-700 via-primary-500 to-primary-400 rounded-3xl p-8 sm:p-10 text-white shadow-2xl shadow-primary-500/30">
                <h1 class="text-3xl sm:text-4xl font-extrabold mb-2 tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-primary-50 text-base sm:text-lg mb-8 font-medium">Selamat datang di Sistem Informasi Pimpinan. Siap mengelola agenda hari ini?</p>

                <!-- Translucent Box -->
                <div class="bg-white/20 backdrop-blur-md border border-white/30 rounded-2xl p-5 inline-block min-w-full sm:min-w-[350px] shadow-inner">
                    <div class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">AGENDA AKTIF HARI INI</div>
                    <div class="text-2xl font-bold mb-2">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-sm font-medium text-white/90">
                        Terdapat <span class="font-bold text-amber-300 text-base">
                            {{ \App\Models\Activity::whereDate('activity_date', \Carbon\Carbon::today())->count() }}
                        </span> kegiatan pimpinan hari ini.
                    </div>
                </div>
            </div>

            <!-- Quick Access Section -->
            <div class="px-2 sm:px-0 mt-8">
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-x-4 gap-y-10">
                    
                    <!-- 1. Agenda Kegiatan -->
                    <a href="{{ route('activities.index') }}" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 shadow-xl shadow-blue-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-blue-600 transition-colors">Agenda<br>Kegiatan</h4>
                    </a>

                    <!-- 2. Master Pimpinan -->
                    <a href="{{ route('leaders.index') }}" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl shadow-emerald-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-emerald-600 transition-colors">Master<br>Pimpinan</h4>
                    </a>

                    <!-- 3. Lokasi Acara -->
                    <a href="{{ route('locations.index') }}" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 shadow-xl shadow-orange-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-orange-600 transition-colors">Lokasi<br>Acara</h4>
                    </a>

                    <!-- 4. Penyelenggara -->
                    <a href="{{ route('organizations.index') }}" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 shadow-xl shadow-purple-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-purple-600 transition-colors">Data<br>Penyelenggara</h4>
                    </a>

                    <!-- 5. Petugas Protokol -->
                    <a href="{{ route('protocol-officers.index') }}" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-pink-400 to-pink-600 shadow-xl shadow-pink-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-pink-600 transition-colors">Petugas<br>Protokol</h4>
                    </a>

                    <!-- 6. Laporan / Statistik -->
                    <a href="#" class="group block text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-500 shadow-xl shadow-yellow-200/60 flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:-translate-y-1">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H4a2 2 0 00-2 2v6a2 2 0 002 2h3a2 2 0 002-2zm0 0V9a2 2 0 012-2h3a2 2 0 012 2v10m-2 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4h4m-4 0v4m0-4v-4m0 0H9m1 0h4"></path></svg>
                        </div>
                        <h4 class="mt-3 font-bold text-[11px] leading-tight text-gray-700 group-hover:text-yellow-600 transition-colors">Statistik &<br>Laporan</h4>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

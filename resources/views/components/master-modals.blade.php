<!-- Modal Tambah Penyelenggara -->
<x-modal name="create-organization" focusable>
    <div class="p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Tambah Penyelenggara Baru</h2>
        <form id="form-create-organization" onsubmit="submitOrganization(event)">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label value="Nama Penyelenggara" />
                    <x-text-input id="org_name" type="text" class="mt-1 block w-full" required />
                    <p id="org_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div>
                    <x-input-label value="Tipe" />
                    <select id="org_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="OPD">Organisasi Perangkat Daerah (OPD)</option>
                        <option value="GOVERNMENT">Instansi Pemerintah</option>
                        <option value="BUMD">Badan Usaha Milik Daerah (BUMD)</option>
                        <option value="PRIVATE">Perusahaan / Swasta</option>
                        <option value="COMMUNITY">Kelompok Masyarakat</option>
                        <option value="ORGANIZATION">Organisasi / Lembaga</option>
                        <option value="EDUCATIONAL">Lembaga Pendidikan</option>
                        <option value="OTHER">Lainnya</option>
                    </select>
                </div>
                <!-- Address removed as requested -->
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                <button type="submit" id="btn_submit_org" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan</button>
            </div>
        </form>
    </div>
</x-modal>

<!-- Modal Tambah Lokasi -->
<x-modal name="create-location" focusable>
    <div class="p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Tambah Lokasi Baru</h2>
        <form id="form-create-location" onsubmit="submitLocation(event)">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label value="Nama Lokasi" />
                    <x-text-input id="loc_name" type="text" class="mt-1 block w-full" required />
                    <p id="loc_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div>
                    <x-input-label value="Alamat" />
                    <textarea id="loc_address" rows="2" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                <button type="submit" id="btn_submit_loc" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan</button>
            </div>
        </form>
    </div>
</x-modal>

<!-- Modal Tambah Pimpinan (Pendamping/Utama) -->
<x-modal name="create-leader" focusable>
    <div class="p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4 border-b pb-2">Tambah Data Pimpinan</h2>
        <form id="form-create-leader" onsubmit="submitLeader(event)">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label value="Nama" />
                    <x-text-input id="ldr_name" type="text" class="mt-1 block w-full" required />
                    <p id="ldr_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div>
                    <x-input-label value="Jabatan" />
                    <x-text-input id="ldr_position" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label value="Level Hierarki" />
                    <x-text-input id="ldr_hierarchy" type="number" class="mt-1 block w-full" value="99" required />
                    <p class="text-xs text-gray-500 mt-1">1=Tertinggi, 2=Bawahnya, dst (Default: 99)</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Batal</button>
                <button type="submit" id="btn_submit_ldr" class="px-4 py-2 bg-secondary-500 text-primary-900 rounded-md font-bold text-xs uppercase hover:bg-secondary-600">Simpan</button>
            </div>
        </form>
    </div>
</x-modal>

<script>
    async function submitOrganization(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_submit_org');
        const errorEl = document.getElementById('org_error');
        const nameInput = document.getElementById('org_name');
        const typeInput = document.getElementById('org_type');
        
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        errorEl.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route('organizations.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name: nameInput.value,
                    type: typeInput.value,
                    address: ''
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Tambahkan ke datalist
                const datalist = document.getElementById('organizers_list');
                const option = document.createElement('option');
                option.value = data.data.name;
                datalist.appendChild(option);
                
                // Isi input form utama
                document.querySelector('input[name="organizer_input"]').value = data.data.name;
                
                // Tutup modal
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-organization' }));
                
                // Reset form
                nameInput.value = '';
                typeInput.value = '';
            } else {
                errorEl.textContent = data.message || 'Terjadi kesalahan.';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            errorEl.textContent = 'Gagal terhubung ke server.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Simpan';
        }
    }

    async function submitLocation(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_submit_loc');
        const errorEl = document.getElementById('loc_error');
        const nameInput = document.getElementById('loc_name');
        const addressInput = document.getElementById('loc_address');
        
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        errorEl.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route('locations.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name: nameInput.value,
                    address: addressInput.value 
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Tambahkan ke datalist
                const datalist = document.getElementById('locations_list');
                const option = document.createElement('option');
                option.value = data.data.name;
                datalist.appendChild(option);
                
                // Isi input form utama
                document.querySelector('input[name="location_input"]').value = data.data.name;
                
                // Tutup modal
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-location' }));
                
                // Reset form
                nameInput.value = '';
                addressInput.value = '';
            } else {
                errorEl.textContent = data.message || 'Terjadi kesalahan.';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            errorEl.textContent = 'Gagal terhubung ke server.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Simpan';
        }
    }

    async function submitLeader(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_submit_ldr');
        const errorEl = document.getElementById('ldr_error');
        const nameInput = document.getElementById('ldr_name');
        const positionInput = document.getElementById('ldr_position');
        const hierarchyInput = document.getElementById('ldr_hierarchy');
        
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        errorEl.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route('leaders.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name: nameInput.value,
                    position: positionInput.value,
                    hierarchy_level: hierarchyInput.value,
                    is_active: true
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                const newLeader = data.data;
                const newText = newLeader.name + ' (' + newLeader.position + ')';
                const newValue = newLeader.id.toString();
                
                // Hitung level berdasarkan jabatan
                let level = 4;
                const pos = newLeader.position.toLowerCase();
                if (pos.includes('wakil bupati')) {
                    level = 2;
                } else if (pos.includes('bupati')) {
                    level = 1;
                } else if (pos.includes('sekda') || pos.includes('sekretaris daerah')) {
                    level = 3;
                }
                
                // Tambahkan ke dropdown utama (Pimpinan Utama)
                if (level <= 3) {
                    const leaderSelect = document.querySelector('select[name="leader_id"]');
                    if (leaderSelect) {
                        const opt = document.createElement('option');
                        opt.value = newValue;
                        opt.textContent = newText;
                        leaderSelect.appendChild(opt);
                    }
                }
                
                // Dispatch event untuk update array options Alpine di Pendamping
                window.dispatchEvent(new CustomEvent('leader-added', { 
                    detail: { value: newValue, text: newText, level: level } 
                }));
                
                // Tutup modal
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'create-leader' }));
                
                // Reset form
                nameInput.value = '';
                positionInput.value = '';
                hierarchyInput.value = '99';
            } else {
                errorEl.textContent = data.message || 'Terjadi kesalahan.';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            errorEl.textContent = 'Gagal terhubung ke server.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Simpan';
        }
    }
</script>

@extends('layouts.app')

@section('title', 'Pendaftaran Pasien')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Pasien</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pasien Baru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Pasien Baru</h3>
            <p class="text-sm text-gray-500 mb-6">Daftarkan pasien baru</p>

            <form method="POST" action="{{ route('registration.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="new">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required maxlength="16"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                        placeholder="16 digit NIK">
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                        placeholder="Nama lengkap pasien">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200">
                        @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200">
                            <option value="">Pilih</option>
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                        placeholder="Nomor HP aktif">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="address" rows="2"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                        placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                        placeholder="Pekerjaan">
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-200">
                    Daftarkan & Ambil Antrean
                </button>
            </form>
        </div>

        <!-- Pasien Lama -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Pasien Lama</h3>
            <p class="text-sm text-gray-500 mb-6">Cari pasien dan ambil antrean</p>

            <!-- Search -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pasien</label>
                <div class="flex gap-2">
                    <input type="text" id="searchPatient" placeholder="Cari No. RM / NIK / Nama..."
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200">
                    <button type="button" onclick="searchPatient()"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Search Results -->
            <div id="searchResults" class="space-y-2"></div>

            <form id="existingPatientForm" method="POST" action="{{ route('registration.store') }}" class="hidden">
                @csrf
                <input type="hidden" name="type" value="existing">
                <input type="hidden" name="patient_id" id="patientId">
                <button type="submit" id="btnTakeQueue"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-200">
                    Ambil Antrean
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .patient-result {
        cursor: pointer;
    }
    .patient-result.selected {
        @apply bg-emerald-50 border-emerald-300;
    }
</style>
@endpush

@push('scripts')
<script>
    let searchTimeout;
    document.getElementById('searchPatient')?.addEventListener('keyup', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchPatient, 500);
    });

    async function searchPatient() {
        const query = document.getElementById('searchPatient').value;
        if (query.length < 2) {
            document.getElementById('searchResults').innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/api/patients/search?q=${encodeURIComponent(query)}`);
            const patients = await response.json();
            const container = document.getElementById('searchResults');

            if (patients.length === 0) {
                container.innerHTML = '<p class="text-gray-400 text-sm text-center py-4">Pasien tidak ditemukan</p>';
                return;
            }

            container.innerHTML = patients.map(p => `
                <div class="patient-result p-3 border border-gray-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 transition-all" onclick="selectPatient(${p.id}, '${p.medical_record_number}', '${p.name.replace(/'/g, "\\'")}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">${p.name}</p>
                            <p class="text-sm text-gray-500">RM: ${p.medical_record_number} | NIK: ${p.nik}</p>
                        </div>
                        <span class="text-xs text-emerald-600 font-medium">Pilih</span>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Search failed:', error);
        }
    }

    function selectPatient(id, rm, name) {
        document.querySelectorAll('.patient-result').forEach(el => el.classList.remove('selected', 'bg-emerald-50', 'border-emerald-300'));
        event.currentTarget.classList.add('selected', 'bg-emerald-50', 'border-emerald-300');

        document.getElementById('patientId').value = id;
        document.getElementById('existingPatientForm').classList.remove('hidden');
        document.getElementById('btnTakeQueue').textContent = `Ambil Antrean - ${name}`;
    }
</script>
@endpush
@endsection
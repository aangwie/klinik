@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Pemeriksaan Pasien</h2>
        <a href="{{ route('examination.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">← Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Data Pasien -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pasien</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">No. Rekam Medis</p>
                        <p class="font-medium text-gray-800">{{ $patient->medical_record_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nama</p>
                        <p class="font-medium text-gray-800">{{ $patient->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">NIK</p>
                        <p class="font-medium text-gray-800">{{ $patient->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Umur</p>
                        <p class="font-medium text-gray-800">{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age . ' tahun' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis Kelamin</p>
                        <p class="font-medium text-gray-800">{{ $patient->gender == 'L' ? 'Laki-laki' : ($patient->gender == 'P' ? 'Perempuan' : '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No. HP</p>
                        <p class="font-medium text-gray-800">{{ $patient->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="font-medium text-gray-800">{{ $patient->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pemeriksaan -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('examination.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Keluhan & Tanda Vital</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan Utama <span class="text-red-500">*</span></label>
                            <textarea name="complaint" rows="3" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                                placeholder="Keluhan pasien">{{ old('complaint') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tekanan Darah</label>
                                <input type="text" name="blood_pressure" value="{{ old('blood_pressure') }}"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                    placeholder="120/80">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
                                <input type="number" name="weight" value="{{ old('weight') }}" step="0.1"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                    placeholder="60">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan (cm)</label>
                                <input type="number" name="height" value="{{ old('height') }}" step="0.1"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                    placeholder="165">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Suhu Tubuh (°C)</label>
                                <input type="number" name="temperature" value="{{ old('temperature') }}" step="0.1"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                    placeholder="36.5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nadi (bpm)</label>
                                <input type="number" name="pulse" value="{{ old('pulse') }}"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                                    placeholder="80">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosa & Tindakan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa <span class="text-red-500">*</span></label>
                            <textarea name="diagnosis" rows="3" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                                placeholder="Hasil diagnosa">{{ old('diagnosis') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan</label>
                            <div class="ts-wrapper-green">
                                <select id="actionSelect" name="actions[]" multiple placeholder="Pilih tindakan..." autocomplete="off">
                                    @forelse($serviceActions as $sa)
                                    <option value="{{ $sa->id }}">{{ $sa->name }}@if(Auth::user()->role == 'admin') (Rp {{ number_format($sa->price, 0, ',', '.') }})@endif</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            @if($serviceActions->isEmpty())
                            <p class="text-sm text-gray-400 mt-1">Belum ada tindakan tersedia</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200"
                                placeholder="Catatan tambahan">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Resep Obat</h3>
                    <div id="prescriptions" class="space-y-3">
                        <div class="prescription-item p-3 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Obat</label>
                                    <select name="medicines[0][id]"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach($medicines ?? [] as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }} (Stok: {{ $m->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                                    <input type="number" name="medicines[0][qty]" min="1" value="1"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Aturan Pakai</label>
                                    <input type="text" name="medicines[0][instruction]"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                                        placeholder="3x sehari 1 tablet">
                                </div>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()"
                                class="mt-2 text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                        </div>
                    </div>
                    <button type="button" onclick="addPrescription()"
                        class="mt-3 text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Obat
                    </button>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('examination.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg shadow-emerald-200">
                        Simpan Pemeriksaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let medIndex = 1;
    function addPrescription() {
        const container = document.getElementById('prescriptions');
        const div = document.createElement('div');
        div.className = 'prescription-item p-3 border border-gray-200 rounded-lg';
        div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Obat</label>
                    <select name="medicines[${medIndex}][id]" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                        <option value="">-- Pilih Obat --</option>
                        @foreach($medicines ?? [] as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} (Stok: {{ $m->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                    <input type="number" name="medicines[${medIndex}][qty]" min="1" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Aturan Pakai</label>
                    <input type="text" name="medicines[${medIndex}][instruction]" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm" placeholder="3x sehari 1 tablet">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="mt-2 text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
        `;
        container.appendChild(div);
        medIndex++;
    }
</script>
@endpush
@endsection
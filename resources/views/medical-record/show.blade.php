@extends('layouts.app')

@section('title', 'Detail Rekam Medis')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Detail Rekam Medis</h2>
        <div class="flex gap-2">
            <a href="{{ route('medical-record.pdf', $patient->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Cetak PDF
            </a>
            <a href="{{ route('medical-record.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 inline-flex items-center gap-2">← Kembali</a>
        </div>
    </div>

    <!-- Data Pasien -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pasien</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500">No. Rekam Medis</p>
                <p class="font-medium text-gray-800">{{ $patient->medical_record_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Nama</p>
                <p class="font-medium text-gray-800">{{ $patient->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">NIK</p>
                <p class="font-medium text-gray-800">{{ $patient->nik }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Jenis Kelamin</p>
                <p class="font-medium text-gray-800">{{ $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tanggal Lahir</p>
                <p class="font-medium text-gray-800">{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Umur</p>
                <p class="font-medium text-gray-800">{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age . ' tahun' : '-' }}</p>
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

    <!-- Riwayat Kunjungan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Kunjungan</h3>

        @forelse($examinations ?? [] as $exam)
        <div class="border border-gray-200 rounded-lg p-4 mb-4 last:mb-0">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($exam->created_at)->isoFormat('dddd, D MMMM Y') }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-sm font-medium text-gray-700">Dokter: {{ $exam->doctor->name ?? '-' }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $exam->status == 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ ucfirst($exam->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Keluhan</p>
                    <p class="text-gray-800">{{ $exam->complaint ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Diagnosa</p>
                    <p class="text-gray-800">{{ $exam->diagnosis ?? '-' }}</p>
                </div>
                @if($exam->blood_pressure || $exam->weight || $exam->temperature)
                <div>
                    <p class="text-gray-500 mb-1">Tanda Vital</p>
                    <p class="text-gray-800">
                        TD: {{ $exam->blood_pressure ?? '-' }} |
                        BB: {{ $exam->weight ?? '-' }} kg |
                        TB: {{ $exam->height ?? '-' }} cm |
                        Suhu: {{ $exam->temperature ?? '-' }}°C |
                        Nadi: {{ $exam->pulse ?? '-' }} bpm
                    </p>
                </div>
                @endif
                @if($exam->actions)
                <div>
                    <p class="text-gray-500 mb-1">Tindakan</p>
                    <p class="text-gray-800">{{ $exam->actions }}</p>
                </div>
                @endif
            </div>

            @if($exam->prescriptions->count() > 0)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-2">Resep Obat</p>
                <div class="space-y-1">
                    @foreach($exam->prescriptions as $pres)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-800">{{ $pres->medicine->name ?? '-' }}</span>
                        <span class="text-gray-400">x{{ $pres->qty }}</span>
                        <span class="text-gray-500 text-xs">{{ $pres->instruction }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($exam->notes)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Catatan Dokter</p>
                <p class="text-sm text-gray-700">{{ $exam->notes }}</p>
            </div>
            @endif
        </div>
        @empty
        <p class="text-center text-gray-400 py-8">Belum ada riwayat kunjungan</p>
        @endforelse
    </div>
</div>
@endsection
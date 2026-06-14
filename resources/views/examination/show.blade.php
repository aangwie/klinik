@extends('layouts.app')

@section('title', 'Detail Pemeriksaan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Detail Pemeriksaan</h2>
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
                        <p class="font-medium text-gray-800">{{ $examination->patient->medical_record_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nama</p>
                        <p class="font-medium text-gray-800">{{ $examination->patient->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">NIK</p>
                        <p class="font-medium text-gray-800">{{ $examination->patient->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis Kelamin</p>
                        <p class="font-medium text-gray-800">{{ $examination->patient->gender == 'L' ? 'Laki-laki' : ($examination->patient->gender == 'P' ? 'Perempuan' : '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No. HP</p>
                        <p class="font-medium text-gray-800">{{ $examination->patient->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="font-medium text-gray-800">{{ $examination->patient->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pemeriksaan</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Dokter</p>
                        <p class="font-medium text-gray-800">{{ $examination->doctor->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($examination->created_at)->isoFormat('dddd, D MMMM Y H:mm') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        @php
                            $statusClass = match($examination->status) {
                                'selesai' => 'bg-emerald-100 text-emerald-800',
                                'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
                                'diperiksa' => 'bg-blue-100 text-blue-800',
                                default => 'bg-amber-100 text-amber-800'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium inline-block mt-1 {{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($examination->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Pemeriksaan -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Keluhan & Tanda Vital -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keluhan & Tanda Vital</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Keluhan Utama</p>
                        <p class="text-gray-800">{{ $examination->complaint }}</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">TD</p>
                            <p class="font-semibold text-gray-800">{{ $examination->blood_pressure ?? '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">BB</p>
                            <p class="font-semibold text-gray-800">{{ $examination->weight ? $examination->weight . ' kg' : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">TB</p>
                            <p class="font-semibold text-gray-800">{{ $examination->height ? $examination->height . ' cm' : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Suhu</p>
                            <p class="font-semibold text-gray-800">{{ $examination->temperature ? $examination->temperature . '°C' : '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Nadi</p>
                            <p class="font-semibold text-gray-800">{{ $examination->pulse ? $examination->pulse . ' bpm' : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosa & Tindakan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosa & Tindakan</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Diagnosa</p>
                        <p class="text-gray-800">{{ $examination->diagnosis }}</p>
                    </div>
                    @if($examination->actions)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tindakan</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(', ', $examination->actions) as $action)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-sm rounded-full">{{ $action }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if($examination->notes)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Catatan</p>
                        <p class="text-gray-800">{{ $examination->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resep Obat -->
            @if($examination->prescriptions->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resep Obat</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium px-2">Nama Obat</th>
                                <th class="pb-3 font-medium px-2">Jumlah</th>
                                <th class="pb-3 font-medium px-2">Aturan Pakai</th>
                                <th class="pb-3 font-medium px-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examination->prescriptions as $pres)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 px-2 font-medium text-gray-800">{{ $pres->medicine->name ?? '-' }}</td>
                                <td class="py-3 px-2 text-gray-600">{{ $pres->qty }}</td>
                                <td class="py-3 px-2 text-gray-600">{{ $pres->instruction ?? '-' }}</td>
                                <td class="py-3 px-2">
                                    @php
                                        $presStatusClass = match($pres->status) {
                                            'menunggu' => 'bg-amber-100 text-amber-800',
                                            'diproses' => 'bg-blue-100 text-blue-800',
                                            'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
                                            'selesai' => 'bg-emerald-100 text-emerald-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $presStatusClass }}">
                                        {{ str_replace('_', ' ', ucfirst($pres->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Informasi Pembayaran -->
            @if($examination->doctorPayment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pembayaran Jasa Dokter</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Biaya Konsultasi</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($examination->doctorPayment->consultation_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($examination->doctorPayment->action_fee > 0)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Biaya Tindakan</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($examination->doctorPayment->action_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-800 font-medium">Total</span>
                        <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($examination->doctorPayment->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="text-gray-600">Status</span>
                        @if($examination->doctorPayment->status == 'lunas')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">Lunas</span>
                        @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium">Menunggu Pembayaran</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
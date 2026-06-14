@extends('layouts.app')

@section('title', 'Apotek')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Apotek</h2>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Resep Obat</th>
                        <th class="pb-3 font-medium px-2">Dokter</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examinations as $exam)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $exam->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $exam->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2">
                            <div class="space-y-1">
                                @foreach($exam->prescriptions as $pres)
                                <div class="flex items-center gap-2 text-sm py-1 {{ !$loop->last ? 'border-b border-gray-100 pb-2' : '' }}">
                                    <span class="text-gray-700 font-medium">{{ $pres->medicine->name ?? '-' }}</span>
                                    <span class="text-gray-400">x{{ $pres->qty }}</span>
                                    <span class="text-gray-400 text-xs italic">{{ $pres->instruction }}</span>
                                    <span class="ml-auto">
                                        @php
                                            $presStatusClass = match($pres->status) {
                                                'menunggu' => 'bg-amber-100 text-amber-800',
                                                'diproses' => 'bg-blue-100 text-blue-800',
                                                'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
                                                'selesai' => 'bg-emerald-100 text-emerald-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $presStatusClass }}">
                                            {{ $pres->status == 'menunggu' ? 'Antri' : ($pres->status == 'diproses' ? 'Di proses' : ($pres->status == 'menunggu_pembayaran' ? 'Tunggu Bayar' : 'Selesai')) }}
                                        </span>
                                    </span>
                                    <div class="flex gap-1">
                                        @if($pres->status == 'menunggu')
                                            @if($exam->can_process)
                                            <form method="POST" action="{{ route('pharmacy.process', $pres->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap">Proses</button>
                                            </form>
                                            @else
                                            <span class="px-2 py-1 bg-gray-400 text-white text-xs font-medium rounded-lg whitespace-nowrap cursor-not-allowed" title="Tunggu pembayaran jasa dokter terlebih dahulu">Proses</span>
                                            @endif
                                        @elseif($pres->status == 'diproses')
                                            @if($exam->can_process)
                                            <form method="POST" action="{{ route('pharmacy.complete', $pres->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors whitespace-nowrap">Siapkan</button>
                                            </form>
                                            @else
                                            <span class="px-2 py-1 bg-gray-400 text-white text-xs font-medium rounded-lg whitespace-nowrap cursor-not-allowed" title="Tunggu pembayaran jasa dokter terlebih dahulu">Siapkan</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-4 px-2 text-gray-600">{{ $exam->doctor->name ?? '-' }}</td>
                        <td class="py-4 px-2">
                            @php
                                $allStatuses = $exam->prescriptions->pluck('status');
                                $overallStatus = match(true) {
                                    $allStatuses->contains('menunggu') => 'menunggu',
                                    $allStatuses->contains('diproses') => 'diproses',
                                    $allStatuses->contains('menunggu_pembayaran') => 'menunggu_pembayaran',
                                    $allStatuses->every(fn($s) => $s == 'selesai') => 'selesai',
                                    default => 'menunggu'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                                {{ $overallStatus == 'menunggu' ? 'bg-amber-100 text-amber-800' : 
                                   ($overallStatus == 'diproses' ? 'bg-blue-100 text-blue-800' :
                                   ($overallStatus == 'menunggu_pembayaran' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800')) }}">
                                {{ $overallStatus == 'menunggu' ? 'Antri' : 
                                   ($overallStatus == 'diproses' ? 'Diproses' : 
                                   ($overallStatus == 'menunggu_pembayaran' ? 'Tunggu Bayar' : 'Selesai')) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">Tidak ada resep yang perlu diproses</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
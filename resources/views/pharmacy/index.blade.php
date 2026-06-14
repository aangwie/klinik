@extends('layouts.app')

@section('title', 'Apotek')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Apotek</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Resep Obat</th>
                        <th class="pb-3 font-medium px-2">Dokter</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions ?? [] as $prescription)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $prescription->examination->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $prescription->examination->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2">
                            <div class="space-y-1">
                                @foreach($prescription->examination->prescriptions ?? [] as $p)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-700">{{ $p->medicine->name ?? '-' }}</span>
                                    <span class="text-gray-400">x{{ $p->qty }}</span>
                                    <span class="text-gray-400 text-xs">{{ $p->instruction }}</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-4 px-2 text-gray-600">{{ $prescription->examination->doctor->name ?? '-' }}</td>
                        <td class="py-4 px-2">
                            @php
                                $statusClass = match($prescription->status) {
                                    'menunggu' => 'bg-amber-100 text-amber-800',
                                    'diproses' => 'bg-blue-100 text-blue-800',
                                    'selesai' => 'bg-emerald-100 text-emerald-800',
                                    'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ str_replace('_', ' ', ucfirst($prescription->status)) }}
                            </span>
                        </td>
                        <td class="py-4 px-2">
                            @if($prescription->status == 'menunggu')
                            <form method="POST" action="{{ route('pharmacy.process', $prescription->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">Proses</button>
                            </form>
                            @elseif($prescription->status == 'diproses')
                            <form method="POST" action="{{ route('pharmacy.complete', $prescription->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Siapkan</button>
                            </form>
                            @elseif($prescription->status == 'menunggu_pembayaran')
                            <span class="text-xs text-purple-600 font-medium">Menunggu Pembayaran</span>
                            @else
                            <span class="text-xs text-emerald-600 font-medium">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Tidak ada resep yang perlu diproses</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
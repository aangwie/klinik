@extends('layouts.app')

@section('title', 'Antrean Pasien')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Antrean Pasien</h2>
        <span class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. Antrean</th>
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Nama Pasien</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Waktu Daftar</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues ?? [] as $queue)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-700 font-bold rounded-lg">
                                {{ $queue->queue_number }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-600 font-mono">{{ $queue->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2">
                            <span class="font-medium text-gray-800">{{ $queue->patient->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-2">
                            @php
                                $statusClass = match($queue->status) {
                                    'menunggu' => 'bg-amber-100 text-amber-800',
                                    'dipanggil' => 'bg-blue-100 text-blue-800',
                                    'diperiksa' => 'bg-purple-100 text-purple-800',
                                    'selesai' => 'bg-emerald-100 text-emerald-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ str_replace('_', ' ', ucfirst($queue->status)) }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-500">{{ $queue->created_at->format('H:i') }}</td>
                        <td class="py-4 px-2">
                            <div class="flex gap-2">
                                @if(Auth::user()->role == 'pendaftaran' || Auth::user()->role == 'admin')
                                    @if($queue->status == 'menunggu')
                                    <form method="POST" action="{{ route('queue.call', $queue->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">Panggil</button>
                                    </form>
                                    @endif
                                @endif
                                @if(Auth::user()->role == 'dokter' || Auth::user()->role == 'admin')
                                    @if($queue->status == 'dipanggil')
                                    <a href="{{ route('examination.create', $queue->patient_id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors inline-block">Periksa</a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Tidak ada antrean hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
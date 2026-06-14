@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Pemeriksaan Pasien</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Nama Pasien</th>
                        <th class="pb-3 font-medium px-2">Keluhan</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examinations ?? [] as $exam)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $exam->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $exam->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-600 max-w-xs truncate">{{ $exam->complaint ?? '-' }}</td>
                        <td class="py-4 px-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $exam->status == 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst($exam->status ?? 'Menunggu') }}
                            </span>
                        </td>
                        <td class="py-4 px-2">
                            <a href="{{ route('examination.show', $exam->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors inline-block">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">Belum ada pemeriksaan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
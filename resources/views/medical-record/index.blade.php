@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Rekam Medis</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Nama Pasien</th>
                        <th class="pb-3 font-medium px-2">NIK</th>
                        <th class="pb-3 font-medium px-2">Total Kunjungan</th>
                        <th class="pb-3 font-medium px-2">Terakhir</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients ?? [] as $patient)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $patient->medical_record_number }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $patient->name }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $patient->nik }}</td>
                        <td class="py-4 px-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $patient->examinations_count ?? 0 }}x
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-500">{{ $patient->last_examination ? \Carbon\Carbon::parse($patient->last_examination)->format('d/m/Y') : '-' }}</td>
                        <td class="py-4 px-2">
                            <a href="{{ route('medical-record.show', $patient->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors inline-block">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Belum ada data rekam medis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
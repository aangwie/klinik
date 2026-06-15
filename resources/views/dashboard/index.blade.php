@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pasien Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPatientsToday ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $newPatientsToday ?? 0 }} pasien baru</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Jasa + Obat</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Antrean Menunggu</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $queueWaiting ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">pasien menunggu</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pasien</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPatients ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">semua pasien</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert: Expiring Medicines & Low Stock -->
    @php
        $hasAlerts = ($expiringMedicines ?? [])->count() > 0 || ($lowStockMedicines ?? [])->count() > 0;
    @endphp

    @if($hasAlerts)
    <div class="space-y-3">
        @if(($lowStockMedicines ?? [])->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-sm text-amber-800 font-medium">
                    ⚠️ {{ count($lowStockMedicines) }} obat dengan stok menipis. 
                    <a href="{{ route('medicine.index') }}" class="underline hover:text-amber-900">Lihat detail</a>
                </p>
            </div>
        </div>
        @endif

        @if(($expiringMedicines ?? [])->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-800 font-medium">
                    🚨 {{ count($expiringMedicines) }} obat akan expired dalam 6 bulan. 
                    <a href="{{ route('report.index', ['period' => 'harian']) }}#analysis" class="underline hover:text-red-900">Cek di Laporan</a>
                </p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Visits -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kunjungan Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100">
                            <th class="pb-3 font-medium">No. RM</th>
                            <th class="pb-3 font-medium">Pasien</th>
                            <th class="pb-3 font-medium">Dokter</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVisits ?? [] as $visit)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-800">{{ $visit->patient->medical_record_number ?? '-' }}</td>
                            <td class="py-3">
                                <span class="text-gray-800 font-medium">{{ $visit->patient->name ?? '-' }}</span>
                            </td>
                            <td class="py-3 text-gray-600">{{ $visit->doctor->name ?? '-' }}</td>
                            <td class="py-3">
                                @php
                                    $statusColors = [
                                        'menunggu' => 'bg-amber-100 text-amber-800',
                                        'diperiksa' => 'bg-blue-100 text-blue-800',
                                        'selesai' => 'bg-emerald-100 text-emerald-800',
                                        'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $color = $statusColors[$visit->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                    {{ str_replace('_', ' ', ucfirst($visit->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Belum ada kunjungan hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Medicines -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Obat Terlaris</h3>
            <div class="space-y-3">
                @forelse($topMedicines ?? [] as $medicine)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $medicine->medicine->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $medicine->total_sold ?? 0 }} terjual</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($medicine->medicine->selling_price ?? 0, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-center text-gray-400 py-8">Belum ada data obat terlaris</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Expiring Medicines Table -->
    @if(($expiringMedicines ?? [])->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">⏰ Obat Akan Expired</h3>
            <a href="{{ route('report.index', ['period' => 'harian']) }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium">Nama Obat</th>
                        <th class="pb-3 font-medium text-right">Stok</th>
                        <th class="pb-3 font-medium text-right">Tgl. Expired</th>
                        <th class="pb-3 font-medium text-right">Sisa Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringMedicines as $m)
                    @php
                        $daysLeft = (int) ceil(now()->diffInDays($m->expired_date, false));
                        $isUrgent = $daysLeft <= 30;
                    @endphp
                    <tr class="border-b border-gray-50">
                        <td class="py-3 text-gray-800 font-medium">{{ $m->name }}</td>
                        <td class="py-3 text-gray-800 text-right">{{ $m->stock }}</td>
                        <td class="py-3 text-right {{ $isUrgent ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $m->expired_date->format('d/m/Y') }}</td>
                        <td class="py-3 text-right">
                            @if($isUrgent)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $daysLeft }} hari</span>
                            @else
                            <span class="text-xs text-gray-500">{{ $daysLeft }} hari lagi</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
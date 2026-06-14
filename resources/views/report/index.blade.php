@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
    </div>

    @php
        $periods = ['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'];
        $activePeriod = request()->get('period', 'harian');
    @endphp

    <!-- Period Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 inline-flex">
        @foreach($periods as $key => $label)
        <a href="{{ route('report.index', ['period' => $key]) }}"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $activePeriod == $key ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($activePeriod == 'harian')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kunjungan Pasien Hari Ini</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="pb-3 font-medium">No.</th>
                            <th class="pb-3 font-medium">Pasien</th>
                            <th class="pb-3 font-medium">Jam</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyVisits ?? [] as $index => $visit)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 font-medium text-gray-800">{{ $visit->patient->name ?? '-' }}</td>
                            <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($visit->created_at)->format('H:i') }}</td>
                            <td class="py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ ucfirst($visit->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-8 text-center text-gray-400">Belum ada kunjungan hari ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                <span class="text-sm text-gray-600 font-medium">Total Kunjungan</span>
                <span class="text-lg font-bold text-gray-800">{{ count($dailyVisits ?? []) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan Hari Ini</h3>
            <div class="space-y-4">
                <div class="bg-emerald-50 rounded-lg p-4">
                    <p class="text-sm text-emerald-600">Pendapatan Jasa Dokter</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($dailyDoctorRevenue ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-600">Pendapatan Obat</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($dailyPharmacyRevenue ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format(($dailyDoctorRevenue ?? 0) + ($dailyPharmacyRevenue ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @elseif($activePeriod == 'bulanan')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan Jasa Dokter Bulan {{ now()->format('F Y') }}</h3>
            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($monthlyDoctorRevenue ?? 0, 0, ',', '.') }}</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="pb-3 font-medium">Tanggal</th>
                            <th class="pb-3 font-medium">Jumlah</th>
                            <th class="pb-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyDoctorDetails ?? [] as $detail)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-600">{{ \Carbon\Carbon::parse($detail->date)->format('d/m/Y') }}</td>
                            <td class="py-3 text-gray-800">{{ $detail->count }} transaksi</td>
                            <td class="py-3 font-semibold text-gray-800">Rp {{ number_format($detail->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-8 text-center text-gray-400">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penjualan Obat Bulan {{ now()->format('F Y') }}</h3>
            <p class="text-3xl font-bold text-emerald-600">Rp {{ number_format($monthlyPharmacyRevenue ?? 0, 0, ',', '.') }}</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="pb-3 font-medium">Obat</th>
                            <th class="pb-3 font-medium">Terjual</th>
                            <th class="pb-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyMedicineSales ?? [] as $sale)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-800">{{ $sale->medicine_name }}</td>
                            <td class="py-3 text-gray-600">{{ $sale->qty }}</td>
                            <td class="py-3 font-semibold text-gray-800">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-8 text-center text-gray-400">Belum ada penjualan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pendapatan Tahunan {{ now()->format('Y') }}</h3>
            <div class="space-y-2">
                @foreach($yearlyRevenue ?? [] as $revenue)
                <div class="flex items-center gap-3">
                    <span class="w-16 text-sm text-gray-600">{{ $revenue->month_name }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        @php
                            $maxRevenue = collect($yearlyRevenue)->max('total');
                            $width = $maxRevenue > 0 ? ($revenue->total / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="bg-emerald-500 rounded-full h-4" style="width: {{ $width }}%"></div>
                    </div>
                    <span class="w-24 text-right text-sm font-semibold text-gray-700">Rp {{ number_format($revenue->total, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @if(empty($yearlyRevenue))
            <p class="text-center text-gray-400 py-8">Belum ada data tahun ini</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Pasien</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Total Pasien Terdaftar</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalPatients ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Total Kunjungan Tahun Ini</span>
                    <span class="text-xl font-bold text-gray-800">{{ $yearlyTotalVisits ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Rata-rata Kunjungan / Bulan</span>
                    <span class="text-xl font-bold text-gray-800">{{ $yearlyTotalVisits > 0 ? round($yearlyTotalVisits / 12) : 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600">Total Pendapatan Tahun Ini</span>
                    <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($yearlyTotalRevenue ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
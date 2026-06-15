@extends('layouts.app')

@section('title', 'Laporan & Analisis')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Laporan & Analisis</h2>
    </div>

    @php
        $periods = ['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'];
        $activePeriod = request()->get('period', 'harian');
    @endphp

    <!-- Period Tabs -->
    <div class="flex items-center gap-4 flex-wrap">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 inline-flex">
            @foreach($periods as $key => $label)
            <a href="{{ route('report.index', ['period' => $key, 'start_date' => $startDate ?? now()->format('Y-m-d'), 'end_date' => $endDate ?? now()->format('Y-m-d'), 'month' => $selectedMonth ?? now()->format('Y-m')]) }}"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $activePeriod == $key ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <!-- Date Range Picker for Harian -->
        @if($activePeriod == 'harian')
        <form action="{{ route('report.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="period" value="harian">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Dari</span>
                <input type="date" name="start_date" value="{{ $startDate ?? now()->format('Y-m-d') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                <span class="text-sm text-gray-500">Sampai</span>
                <input type="date" name="end_date" value="{{ $endDate ?? now()->format('Y-m-d') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </form>
        @endif

        <!-- Month Picker for Bulanan -->
        @if($activePeriod == 'bulanan')
        <form action="{{ route('report.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="period" value="bulanan">
            <span class="text-sm text-gray-500">Bulan</span>
            <input type="month" name="month" value="{{ $selectedMonth ?? now()->format('Y-m') }}"
                onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
        </form>
        @endif
    </div>

    @if($activePeriod == 'harian')
    <div class="flex items-center gap-2 mb-2">
        <p class="text-sm text-gray-500">Periode: 
            <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</strong>
        </p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kunjungan Pasien</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="pb-3 font-medium">No.</th>
                            <th class="pb-3 font-medium">Pasien</th>
                            <th class="pb-3 font-medium">Tanggal</th>
                            <th class="pb-3 font-medium">Jam</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyVisits ?? [] as $index => $visit)
                        <tr class="border-b border-gray-50">
                            <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 font-medium text-gray-800">{{ $visit->patient->name ?? '-' }}</td>
                            <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($visit->created_at)->format('d/m/Y') }}</td>
                            <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($visit->created_at)->format('H:i') }}</td>
                            <td class="py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ ucfirst($visit->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada kunjungan</td></tr>
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan Periode</h3>
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan Jasa Dokter Bulan {{ \Carbon\Carbon::parse($selectedMonth . '-01')->isoFormat('MMMM Y') }}</h3>
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penjualan Obat Bulan {{ \Carbon\Carbon::parse($selectedMonth . '-01')->isoFormat('MMMM Y') }}</h3>
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

    <!-- ANALYSIS SECTION -->
    <div class="border-t-2 border-emerald-200 pt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">📊 Analisis Data Klinik</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Tindakan yang sering dilakukan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔬 Tindakan Paling Sering</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">No.</th>
                                <th class="pb-3 font-medium">Tindakan</th>
                                <th class="pb-3 font-medium text-right">Frekuensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actionAnalysis ?? [] as $index => $action)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-3 text-gray-800">{{ $action->actions }}</td>
                                <td class="py-3 text-gray-800 font-semibold text-right">{{ $action->total }}x</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-8 text-center text-gray-400">Belum ada data tindakan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Obat paling sering diresepkan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">💊 Obat Paling Sering Diresepkan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">No.</th>
                                <th class="pb-3 font-medium">Nama Obat</th>
                                <th class="pb-3 font-medium text-right">Total Terjual</th>
                                <th class="pb-3 font-medium text-right">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicineAnalysis ?? [] as $index => $ma)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-3 text-gray-800 font-medium">{{ $ma->medicine_name }}</td>
                                <td class="py-3 text-gray-800 text-right">{{ $ma->total_qty }} unit</td>
                                <td class="py-3 font-semibold text-gray-800 text-right">Rp {{ number_format($ma->total_value, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400">Belum ada data penjualan obat</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Obat stok menipis -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">⚠️ Obat Stok Menipis (Perlu Order Ulang)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">No.</th>
                                <th class="pb-3 font-medium">Nama Obat</th>
                                <th class="pb-3 font-medium text-right">Stok</th>
                                <th class="pb-3 font-medium text-right">Min. Stok</th>
                                <th class="pb-3 font-medium text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockMedicines ?? [] as $index => $m)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-3 text-gray-800 font-medium">{{ $m->name }}</td>
                                <td class="py-3 text-gray-800 text-right {{ $m->stock <= 0 ? 'text-red-600 font-bold' : 'text-amber-600 font-semibold' }}">{{ $m->stock }}</td>
                                <td class="py-3 text-gray-500 text-right">{{ $m->low_stock }}</td>
                                <td class="py-3 text-right">
                                    @if($m->stock <= 0)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Habis</span>
                                    @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menipis</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">Semua obat dalam stok cukup</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-3">* Stok dianggap menipis jika stok ≤ batas minimal stok yang ditentukan</p>
            </div>

            <!-- Obat mendekati expired -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">⏰ Obat Akan Expired (6 Bulan ke Depan)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">No.</th>
                                <th class="pb-3 font-medium">Nama Obat</th>
                                <th class="pb-3 font-medium text-right">Stok</th>
                                <th class="pb-3 font-medium text-right">Tgl. Expired</th>
                                <th class="pb-3 font-medium text-right">Sisa Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringMedicines ?? [] as $index => $m)
                            @php
                                $daysLeft = (int) ceil(now()->diffInDays($m->expired_date, false));
                                $isUrgent = $daysLeft <= 30;
                            @endphp
                            <tr class="border-b border-gray-50">
                                <td class="py-3 text-gray-600">{{ $index + 1 }}</td>
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
                            @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">Tidak ada obat yang akan expired dalam 6 bulan ke depan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-3">* Menampilkan obat dengan masa expired ≤ 6 bulan dari tanggal sekarang</p>
            </div>
        </div>
    </div>
</div>
@endsection
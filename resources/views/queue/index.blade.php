@extends('layouts.app')

@section('title', 'Antrean Pasien')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between flex-wrap gap-3">
        <h2 class="text-2xl font-bold text-gray-800">Antrean Pasien</h2>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'pendaftaran')
            <div class="flex items-center gap-2 ml-4 pl-4 border-l border-gray-200">
                <input type="date" id="resetDate" class="px-3 py-1.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm">
                <button onclick="confirmReset()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Antrean
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. Antrean</th>
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Nama Pasien</th>
                        <th class="pb-3 font-medium px-2">Dokter</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Waktu Daftar</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues ?? [] as $queue)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors {{ $queue->status == 'batal' ? 'bg-red-50/50 hover:bg-red-50/80' : '' }}">
                        <td class="py-4 px-2">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg font-bold {{ $queue->status == 'batal' ? 'bg-red-100 text-red-500 line-through' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $queue->queue_number }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-600 font-mono">{{ $queue->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2">
                            <span class="font-medium {{ $queue->status == 'batal' ? 'text-gray-400 line-through' : 'text-gray-800' }}">{{ $queue->patient->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-2">
                            @if($queue->doctorProfile)
                            <div class="flex items-center gap-1.5">
                                <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xs uppercase">{{ substr($queue->doctorProfile->full_name, 0, 1) }}</span>
                                <span class="text-gray-600 text-xs">{{ $queue->doctorProfile->full_name }}</span>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            @php
                                $statusClass = match($queue->status) {
                                    'menunggu' => 'bg-amber-100 text-amber-800',
                                    'dipanggil' => 'bg-blue-100 text-blue-800',
                                    'diperiksa' => 'bg-purple-100 text-purple-800',
                                    'selesai' => 'bg-emerald-100 text-emerald-800',
                                    'batal' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $queue->status == 'batal' ? 'Batal' : str_replace('_', ' ', ucfirst($queue->status)) }}
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
                                @if(in_array($queue->status, ['menunggu', 'dipanggil']) && (Auth::user()->role == 'pendaftaran' || Auth::user()->role == 'admin'))
                                <button onclick="confirmCancel({{ $queue->id }}, '{{ $queue->queue_number }}', '{{ $queue->patient->name }}')"
                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    Batalkan
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">Tidak ada antrean hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Batalkan Antrean?</h3>
            <p id="cancelPatientInfo" class="text-sm text-gray-500 mt-2"></p>
        </div>
        <form id="cancelForm" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan</label>
                <select name="reason" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all">
                    <option value="">Pilih alasan</option>
                    <option value="pasien_tidak_datang">Pasien tidak datang</option>
                    <option value="pasien_pulang">Pasien pulang / mundur</option>
                    <option value="salah_daftar">Salah pendaftaran</option>
                    <option value="dokter_tidak_ada">Dokter tidak tersedia</option>
                    <option value="duplikat">Antrean duplikat</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeCancelModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Tutup</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-red-200">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function confirmCancel(id, queueNumber, patientName) {
        document.getElementById('cancelPatientInfo').textContent = `Antrean ${queueNumber} - ${patientName}`;
        document.getElementById('cancelForm').action = `/queue/${id}/cancel`;
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
    }

    document.getElementById('cancelModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
</script>
@endpush
@endsection
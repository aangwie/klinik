@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Pembayaran</h2>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Pembayaran Total (Jasa + Obat)</h3>
        <p class="text-sm text-gray-500 mb-4">Pembayaran mencakup biaya konsultasi, tindakan, dan obat-obatan</p>
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Jasa Dokter</th>
                        <th class="pb-3 font-medium px-2">Biaya Obat</th>
                        <th class="pb-3 font-medium px-2">Total</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctorPayments as $dp)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $dp->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $dp->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-800">Rp {{ number_format($dp->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 text-gray-800">Rp {{ number_format($dp->medicine_total ?? 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 font-bold text-emerald-600">Rp {{ number_format($dp->grand_total ?? $dp->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            @if($dp->status == 'lunas')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Lunas</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex gap-1">
                                <button onclick="openDetailModal({{ $dp->id }})"
                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">Detail</button>
                                @if($dp->status == 'menunggu')
                                <button onclick="openTotalPaymentModal({{ $dp->id }}, {{ $dp->grand_total ?? $dp->total }}, '{{ $dp->patient->name }}')"
                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Bayar</button>
                                @elseif($dp->status == 'lunas')
                                <button onclick="printStruk({{ $dp->id }})"
                                    class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">Struk</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">Tidak ada data pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Rincian Pembayaran</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="detailContent" class="space-y-4">
            <div class="text-center py-8 text-gray-400">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Total Payment Modal -->
<div id="totalPaymentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Konfirmasi Pembayaran</h3>
        <p id="totalPaymentPatientName" class="text-sm text-gray-500 mb-6"></p>

        <div class="bg-emerald-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-emerald-600">Total Seluruh Pembayaran</p>
            <p id="totalPaymentAmount" class="text-3xl font-bold text-emerald-600 mt-1"></p>
        </div>

        <form id="totalPaymentForm" method="POST" action="{{ route('payment.total.process') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="doctor_payment_id" id="totalPaymentDoctorId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="">Pilih metode</option>
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                    <option value="debit">Debit</option>
                </select>
            </div>
            <p class="text-xs text-gray-400">Pembayaran ini mencakup jasa dokter, tindakan, dan obat-obatan</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeTotalPaymentModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Konfirmasi Bayar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openDetailModal(doctorPaymentId) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
    document.getElementById('detailContent').innerHTML = '<div class="text-center py-8 text-gray-400">Memuat data...</div>';

    fetch(`/payment/detail/${doctorPaymentId}`)
        .then(res => res.json())
        .then(data => {
            let html = `
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-xs text-gray-500">Pasien</p>
                    <p class="font-semibold text-gray-800">${data.patient.name} (${data.patient.medical_record_number})</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-800 mb-3">Jasa & Tindakan Dokter</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Biaya Konsultasi</span>
                            <span class="font-medium text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(data.consultation_fee)}</span>
                        </div>
                        ${data.actions ? `
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tindakan: ${data.actions}</span>
                            <span class="font-medium text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(data.action_fee)}</span>
                        </div>` : ''}
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                            <span class="font-medium text-gray-700">Subtotal Jasa</span>
                            <span class="font-semibold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(data.total)}</span>
                        </div>
                    </div>
                </div>`;

            if (data.medicines && data.medicines.length > 0) {
                html += `
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-800 mb-3">Obat-obatan</h4>
                    <div class="space-y-2">
                        ${data.medicines.map(m => `
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">${m.name} x${m.qty}</span>
                            <span class="font-medium text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(m.subtotal)}</span>
                        </div>`).join('')}
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                            <span class="font-medium text-gray-700">Subtotal Obat</span>
                            <span class="font-semibold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(data.medicine_total)}</span>
                        </div>
                    </div>
                </div>`;
            }

            html += `
                <div class="bg-emerald-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800">Grand Total</span>
                        <span class="text-xl font-bold text-emerald-600">Rp ${new Intl.NumberFormat('id-ID').format(data.grand_total)}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-500">Status</span>
                        <span class="font-medium ${data.status === 'lunas' ? 'text-emerald-600' : 'text-amber-600'}">${data.status === 'lunas' ? 'Lunas' : 'Menunggu Pembayaran'}</span>
                    </div>
                    ${data.payment_method ? `<div class="flex justify-between text-sm mt-1"><span class="text-gray-500">Metode</span><span class="font-medium text-gray-700 uppercase">${data.payment_method}</span></div>` : ''}
                </div>
                <div class="flex justify-end pt-2">
                    <button onclick="closeDetailModal()" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Tutup</button>
                </div>`;

            document.getElementById('detailContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('detailContent').innerHTML = '<div class="text-center py-8 text-red-400">Gagal memuat data</div>';
        });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

function openTotalPaymentModal(doctorPaymentId, total, patientName) {
    document.getElementById('totalPaymentDoctorId').value = doctorPaymentId;
    document.getElementById('totalPaymentAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    document.getElementById('totalPaymentPatientName').textContent = patientName;
    document.getElementById('totalPaymentModal').classList.remove('hidden');
    document.getElementById('totalPaymentModal').classList.add('flex');
}

function closeTotalPaymentModal() {
    document.getElementById('totalPaymentModal').classList.add('hidden');
    document.getElementById('totalPaymentModal').classList.remove('flex');
}

function printStruk(id) {
    window.open(`/payment/struk/${id}`, '_blank', 'width=400,height=700');
}

document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});
document.getElementById('totalPaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTotalPaymentModal();
});
</script>
@endpush
@endsection
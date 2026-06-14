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

    @php
        $tabs = [
            'total' => 'Total Pembayaran',
            'jasa' => 'Jasa Dokter',
            'obat' => 'Pembayaran Obat',
        ];
        $activeTab = request()->get('tab', 'total');
    @endphp

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 inline-flex flex-wrap">
        @foreach($tabs as $key => $label)
        <a href="{{ route('payment.index', ['tab' => $key]) }}"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $activeTab == $key ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($activeTab == 'total')
    <!-- TAB TOTAL PEMBAYARAN -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Semua Pembayaran Menunggu</h3>
        <p class="text-sm text-gray-500 mb-4">Total keseluruhan biaya yang harus dibayar pasien</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Rincian</th>
                        <th class="pb-3 font-medium px-2">Jasa Dokter</th>
                        <th class="pb-3 font-medium px-2">Biaya Obat</th>
                        <th class="pb-3 font-medium px-2">Total</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($allPayments as $payment)
                    @php $grandTotal += $payment->total; @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $payment->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $payment->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $payment->description }}</td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($payment->doctor_fee ?? 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($payment->medicine_total ?? 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 font-semibold text-emerald-600">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            @if($payment->type == 'doctor')
                                @if($payment->status == 'menunggu')
                                <button onclick="openTotalPaymentModal({{ $payment->id }}, {{ $payment->total }}, '{{ $payment->patient->name }}')"
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Bayar</button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">Tidak ada pembayaran yang menunggu</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($allPayments->count() > 0)
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="5" class="py-4 px-2 text-right text-gray-700 font-semibold">Grand Total</td>
                        <td class="py-4 px-2 font-bold text-lg text-emerald-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @elseif($activeTab == 'jasa')
    <!-- TAB JASA DOKTER -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Tindakan</th>
                        <th class="pb-3 font-medium px-2">Biaya Konsultasi</th>
                        <th class="pb-3 font-medium px-2">Biaya Tindakan</th>
                        <th class="pb-3 font-medium px-2">Total Jasa</th>
                        <th class="pb-3 font-medium px-2">Biaya Obat</th>
                        <th class="pb-3 font-medium px-2">Grand Total</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctorPayments as $payment)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $payment->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $payment->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $payment->examination->actions ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($payment->consultation_fee, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($payment->action_fee, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 font-semibold text-gray-800">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($payment->medicine_total ?? 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 font-bold text-emerald-600">Rp {{ number_format($payment->grand_total ?? $payment->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            @if($payment->status == 'lunas')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Lunas</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex gap-1">
                                @if(Auth::user()->role == 'admin' && $payment->status != 'lunas')
                                <button onclick="openEditFeeModal({{ $payment->id }})"
                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">Edit Biaya</button>
                                @endif
                                @if($payment->status != 'lunas')
                                <button onclick="openPaymentModal('doctor', {{ $payment->id }}, {{ $payment->total }}, '{{ $payment->patient->name }}')"
                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Bayar</button>
                                @else
                                <button onclick="printStruk('doctor', {{ $payment->id }})"
                                    class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">Struk</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-gray-400">Tidak ada pembayaran jasa dokter</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @else
    <!-- TAB OBAT -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">No. RM</th>
                        <th class="pb-3 font-medium px-2">Pasien</th>
                        <th class="pb-3 font-medium px-2">Obat</th>
                        <th class="pb-3 font-medium px-2">Total</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pharmacyPayments as $payment)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $payment->patient->medical_record_number ?? '-' }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $payment->patient->name ?? '-' }}</td>
                        <td class="py-4 px-2 text-gray-600 max-w-[200px] truncate">
                            {{ $payment->prescription->medicine->name ?? '-' }} x{{ $payment->prescription->qty ?? 0 }}
                        </td>
                        <td class="py-4 px-2 font-semibold text-gray-800">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            @if($payment->status == 'lunas')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Lunas</span>
                            @elseif($payment->status == 'menunggu_pembayaran')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Menunggu</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            @if($payment->status == 'menunggu_pembayaran')
                            <button onclick="openPaymentModal('pharmacy', {{ $payment->id }}, {{ $payment->total }}, '{{ $payment->patient->name }}')"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Bayar</button>
                            @elseif($payment->status == 'lunas')
                            <button onclick="printStruk('pharmacy', {{ $payment->id }})"
                                class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">Struk</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Tidak ada pembayaran obat</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Payment Modal (for doctor/pharmacy individual) -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Konfirmasi Pembayaran</h3>
        <p id="paymentPatientName" class="text-sm text-gray-500 mb-6"></p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Total Pembayaran</p>
            <p id="paymentTotal" class="text-2xl font-bold text-gray-800 mt-1"></p>
        </div>

        <form id="paymentForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="payment_id" id="paymentId">
            <input type="hidden" name="payment_type" id="paymentType">
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
            <div class="flex gap-3">
                <button type="button" onclick="closePaymentModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Konfirmasi Bayar</button>
            </div>
        </form>
    </div>
</div>

<!-- Total Payment Modal -->
<div id="totalPaymentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-1">Konfirmasi Pembayaran Total</h3>
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
                    class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Konfirmasi Bayar Semua</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Fee Modal (Admin only) -->
<div id="editFeeModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Edit Biaya Jasa Dokter</h3>

        <form id="editFeeForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Konsultasi</label>
                <input type="number" name="consultation_fee" id="editConsultationFee" min="0" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Tindakan</label>
                <input type="number" name="action_fee" id="editActionFee" min="0" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-600">Total</p>
                <p id="editTotalDisplay" class="text-xl font-bold text-gray-800"></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditFeeModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Payment modal functions
    function openPaymentModal(type, id, total, patientName) {
        document.getElementById('paymentType').value = type;
        document.getElementById('paymentId').value = id;
        document.getElementById('paymentTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        document.getElementById('paymentPatientName').textContent = patientName;
        document.getElementById('paymentForm').action = type === 'doctor'
            ? '{{ route("payment.doctor.process") }}'
            : '{{ route("payment.pharmacy.process") }}';
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
    }

    // Total payment modal functions
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

    // Edit fee modal functions
    function openEditFeeModal(id) {
        fetch(`/payment/doctor/fee/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editFeeForm').action = `/payment/doctor/fee/${id}`;
                document.getElementById('editConsultationFee').value = data.consultation_fee;
                document.getElementById('editActionFee').value = data.action_fee;
                updateEditTotal();
                document.getElementById('editFeeModal').classList.remove('hidden');
                document.getElementById('editFeeModal').classList.add('flex');
            });
    }

    function closeEditFeeModal() {
        document.getElementById('editFeeModal').classList.add('hidden');
        document.getElementById('editFeeModal').classList.remove('flex');
    }

    function updateEditTotal() {
        const consultation = parseInt(document.getElementById('editConsultationFee').value) || 0;
        const action = parseInt(document.getElementById('editActionFee').value) || 0;
        const total = consultation + action;
        document.getElementById('editTotalDisplay').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    document.getElementById('editConsultationFee')?.addEventListener('input', updateEditTotal);
    document.getElementById('editActionFee')?.addEventListener('input', updateEditTotal);

    // Print struk
    function printStruk(type, id) {
        window.open(`/payment/${type}/struk/${id}`, '_blank', 'width=400,height=600');
    }
</script>
@endpush
@endsection
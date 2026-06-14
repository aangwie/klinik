@extends('layouts.app')

@section('title', 'Manajemen Obat')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Obat</h2>
        <div class="flex gap-2">
            <a href="{{ route('medicine.export') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Obat
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">Kode</th>
                        <th class="pb-3 font-medium px-2">Nama Obat</th>
                        <th class="pb-3 font-medium px-2">Kategori</th>
                        <th class="pb-3 font-medium px-2">Satuan</th>
                        <th class="pb-3 font-medium px-2">Stok</th>
                        <th class="pb-3 font-medium px-2">Harga Beli</th>
                        <th class="pb-3 font-medium px-2">Harga Jual</th>
                        <th class="pb-3 font-medium px-2">Expired</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines ?? [] as $medicine)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $medicine->code }}</td>
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $medicine->name }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $medicine->category }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $medicine->unit }}</td>
                        <td class="py-4 px-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $medicine->stock <= 5 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $medicine->stock }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-600">Rp {{ number_format($medicine->purchase_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 font-semibold text-gray-800">Rp {{ number_format($medicine->selling_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-2 text-gray-600">{{ $medicine->expired_date ? \Carbon\Carbon::parse($medicine->expired_date)->format('d/m/Y') : '-' }}</td>
                        <td class="py-4 px-2">
                            <div class="flex gap-2">
                                <button onclick="editMedicine({{ $medicine->id }})" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('medicine.destroy', $medicine->id) }}" onsubmit="return confirm('Hapus obat ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-400">Belum ada data obat</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Obat -->
<div id="medicineModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-6">Tambah Obat</h3>

        <form id="medicineForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="medicine_id" id="medicineId">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Obat <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="medCode" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Obat <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="medName" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <input type="text" name="category" id="medCategory"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                        placeholder="Tablet, Sirup, dll">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="unit" id="medUnit"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                        placeholder="botol, strip, dll">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="stock" id="medStock" min="0"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                    <input type="number" name="purchase_price" id="medPurchasePrice" min="0"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
                    <input type="number" name="selling_price" id="medSellingPrice" min="0" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Expired</label>
                <input type="date" name="expired_date" id="medExpired"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Obat';
        document.getElementById('medicineForm').action = '{{ route("medicine.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('medicineId').value = '';
        document.getElementById('medCode').value = '';
        document.getElementById('medName').value = '';
        document.getElementById('medCategory').value = '';
        document.getElementById('medUnit').value = '';
        document.getElementById('medStock').value = '';
        document.getElementById('medPurchasePrice').value = '';
        document.getElementById('medSellingPrice').value = '';
        document.getElementById('medExpired').value = '';
        document.getElementById('medicineModal').classList.remove('hidden');
        document.getElementById('medicineModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('medicineModal').classList.add('hidden');
        document.getElementById('medicineModal').classList.remove('flex');
    }

    function editMedicine(id) {
        fetch(`/medicine/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Obat';
                document.getElementById('medicineForm').action = `/medicine/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('medicineId').value = id;
                document.getElementById('medCode').value = data.code;
                document.getElementById('medName').value = data.name;
                document.getElementById('medCategory').value = data.category;
                document.getElementById('medUnit').value = data.unit;
                document.getElementById('medStock').value = data.stock;
                document.getElementById('medPurchasePrice').value = data.purchase_price;
                document.getElementById('medSellingPrice').value = data.selling_price;
                document.getElementById('medExpired').value = data.expired_date;
                document.getElementById('medicineModal').classList.remove('hidden');
                document.getElementById('medicineModal').classList.add('flex');
            });
    }
</script>
@endpush
@endsection
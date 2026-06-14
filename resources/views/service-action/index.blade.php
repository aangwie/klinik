@extends('layouts.app')

@section('title', 'Manajemen Jasa & Tindakan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Jasa & Tindakan</h2>
        <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="datatable w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">Nama</th>
                        <th class="pb-3 font-medium px-2">Deskripsi</th>
                        <th class="pb-3 font-medium px-2">Harga</th>
                        <th class="pb-3 font-medium px-2">Status</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceActions as $action)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2 font-medium text-gray-800">{{ $action->name }}</td>
                        <td class="py-4 px-2 text-gray-600 max-w-xs">{{ $action->description ?? '-' }}</td>
                        <td class="py-4 px-2 font-semibold text-gray-800">Rp {{ number_format($action->price, 0, ',', '.') }}</td>
                        <td class="py-4 px-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $action->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $action->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-4 px-2">
                            <div class="flex gap-2">
                                <button onclick="editAction({{ $action->id }})" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('service-action.toggle', $action->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-{{ $action->is_active ? 'amber' : 'emerald' }}-600 hover:text-{{ $action->is_active ? 'amber' : 'emerald' }}-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('service-action.destroy', $action->id) }}" onsubmit="return confirm('Hapus {{ $action->name }}?')" class="inline">
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
                        <td colspan="5" class="py-12 text-center text-gray-400">Belum ada data jasa/tindakan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="actionModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-6">Tambah Jasa/Tindakan</h3>

        <form id="actionForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="action_id" id="actionId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="actionName" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="Nama jasa/tindakan">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="actionDescription" rows="2"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="Deskripsi (opsional)"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="price" id="actionPrice" min="0" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="0">
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
        document.getElementById('modalTitle').textContent = 'Tambah Jasa/Tindakan';
        document.getElementById('actionForm').action = '{{ route("service-action.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('actionId').value = '';
        document.getElementById('actionName').value = '';
        document.getElementById('actionDescription').value = '';
        document.getElementById('actionPrice').value = '';
        document.getElementById('actionModal').classList.remove('hidden');
        document.getElementById('actionModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('actionModal').classList.add('hidden');
        document.getElementById('actionModal').classList.remove('flex');
    }

    function editAction(id) {
        fetch(`/service-action/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Jasa/Tindakan';
                document.getElementById('actionForm').action = `/service-action/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('actionId').value = id;
                document.getElementById('actionName').value = data.name;
                document.getElementById('actionDescription').value = data.description || '';
                document.getElementById('actionPrice').value = data.price;
                document.getElementById('actionModal').classList.remove('hidden');
                document.getElementById('actionModal').classList.add('flex');
            });
    }
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Profil Dokter')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Profil Dokter</h2>
        <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Profil
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($profiles as $profile)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg uppercase">
                    {{ substr($profile->full_name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $profile->full_name }}</h3>
                    <p class="text-xs text-gray-500">{{ $profile->specialization ?? 'Dokter Umum' }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ $profile->user->name ?? '-' }}</span>
                </div>
                @if($profile->birth_place || $profile->birth_date)
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $profile->birth_place ? $profile->birth_place . ', ' : '' }}{{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('d/m/Y') : '' }}</span>
                </div>
                @endif
                @if($profile->consultation_fee)
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($profile->consultation_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $profile->is_available ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                        <span class="w-2 h-2 rounded-full {{ $profile->is_available ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        {{ $profile->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </div>
                @if($profile->phone)
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $profile->phone }}</span>
                </div>
                @endif
                @if($profile->str_number)
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-mono text-xs">STR: {{ $profile->str_number }}</span>
                </div>
                @endif
                @if($profile->address)
                <div class="flex items-start gap-2 text-gray-600">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-xs">{{ $profile->address }}</span>
                </div>
                @endif
            </div>
            <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                <button onclick="editProfile({{ $profile->id }})" class="flex-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors text-center">Edit</button>
                <form method="POST" action="{{ route('doctor-profile.toggle', $profile->id) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-1.5 {{ $profile->is_available ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white text-xs font-medium rounded-lg transition-colors">
                        {{ $profile->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('doctor-profile.destroy', $profile->id) }}" onsubmit="return confirm('Hapus profil {{ $profile->full_name }}?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <p>Belum ada data profil dokter</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="profileModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-6">Tambah Profil Dokter</h3>
        <form id="profileForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="profile_id" id="profileId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Akun Pengguna <span class="text-red-500">*</span></label>
                <select name="user_id" id="profileUser" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="">Pilih akun dokter</option>
                    @foreach($doctors as $d)
                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->username }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" id="profileName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Nama lengkap dokter">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                <input type="text" name="specialization" id="profileSpec" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Dokter Umum">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="profileBirthPlace" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Kota">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="profileBirthDate" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="phone" id="profilePhone" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Nomor HP">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor STR</label>
                <input type="text" name="str_number" id="profileStr" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Nomor STR dokter">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Konsultasi (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="consultation_fee" id="profileFee" min="0" required value="50000" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="50000">
                <p class="text-xs text-gray-400 mt-1">Biaya konsultasi dokter ini per kunjungan</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" id="profileAddress" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Alamat lengkap"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-lg shadow-emerald-200">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Profil Dokter';
    document.getElementById('profileForm').action = '{{ route("doctor-profile.store") }}';
    document.getElementById('methodField').value = 'POST';
    document.getElementById('profileId').value = '';
    document.getElementById('profileUser').value = '';
    document.getElementById('profileName').value = '';
    document.getElementById('profileSpec').value = '';
    document.getElementById('profileBirthPlace').value = '';
    document.getElementById('profileBirthDate').value = '';
    document.getElementById('profilePhone').value = '';
    document.getElementById('profileStr').value = '';
    document.getElementById('profileAddress').value = '';
    document.getElementById('profileModal').classList.remove('hidden');
    document.getElementById('profileModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('profileModal').classList.add('hidden');
    document.getElementById('profileModal').classList.remove('flex');
}
function editProfile(id) {
    fetch(`/doctor-profile/${id}/edit`).then(r=>r.json()).then(d => {
        document.getElementById('modalTitle').textContent = 'Edit Profil Dokter';
        document.getElementById('profileForm').action = `/doctor-profile/${id}`;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('profileId').value = id;
        document.getElementById('profileUser').value = d.user_id;
        document.getElementById('profileName').value = d.full_name;
        document.getElementById('profileSpec').value = d.specialization || '';
        document.getElementById('profileBirthPlace').value = d.birth_place || '';
        document.getElementById('profileBirthDate').value = d.birth_date || '';
        document.getElementById('profilePhone').value = d.phone || '';
        document.getElementById('profileStr').value = d.str_number || '';
        document.getElementById('profileFee').value = d.consultation_fee || 50000;
        document.getElementById('profileAddress').value = d.address || '';
        document.getElementById('profileModal').classList.remove('hidden');
        document.getElementById('profileModal').classList.add('flex');
    });
}
</script>
@endpush
@endsection
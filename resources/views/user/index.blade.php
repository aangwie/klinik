@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
        <button onclick="openModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="pb-3 font-medium px-2">Nama</th>
                        <th class="pb-3 font-medium px-2">Username</th>
                        <th class="pb-3 font-medium px-2">Role</th>
                        <th class="pb-3 font-medium px-2">Terdaftar</th>
                        <th class="pb-3 font-medium px-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-semibold text-sm uppercase
                                    {{ $user->role == 'admin' ? 'bg-red-500' : ($user->role == 'dokter' ? 'bg-blue-500' : ($user->role == 'kasir' ? 'bg-amber-500' : ($user->role == 'apoteker' ? 'bg-purple-500' : 'bg-emerald-500'))) }}">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-2 font-mono text-gray-600">{{ $user->username }}</td>
                        <td class="py-4 px-2">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-800',
                                    'pendaftaran' => 'bg-emerald-100 text-emerald-800',
                                    'dokter' => 'bg-blue-100 text-blue-800',
                                    'kasir' => 'bg-amber-100 text-amber-800',
                                    'apoteker' => 'bg-purple-100 text-purple-800',
                                ];
                                $roleLabels = [
                                    'admin' => 'Admin',
                                    'pendaftaran' => 'Pendaftaran',
                                    'dokter' => 'Dokter',
                                    'kasir' => 'Kasir',
                                    'apoteker' => 'Apoteker',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="py-4 px-2">
                            <div class="flex gap-2">
                                <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($user->id != auth()->id())
                                <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">Belum ada data pengguna</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Pengguna -->
<div id="userModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-6">Tambah Pengguna</h3>

        <form id="userForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="user_id" id="userId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="userName" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="Nama pengguna">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="userUsername" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="Username untuk login">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500" id="passRequired">*</span></label>
                <input type="password" name="password" id="userPassword" minlength="6"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                    placeholder="Minimal 6 karakter">
                <p class="text-xs text-gray-400 mt-1" id="passHelp">Kosongkan jika tidak ingin mengubah password</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select name="role" id="userRole" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="pendaftaran">Pendaftaran</option>
                    <option value="dokter">Dokter</option>
                    <option value="kasir">Kasir</option>
                    <option value="apoteker">Apoteker</option>
                </select>
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
        document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
        document.getElementById('userForm').action = '{{ route("user.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('userUsername').value = '';
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = true;
        document.getElementById('passRequired').style.display = 'inline';
        document.getElementById('passHelp').textContent = 'Minimal 6 karakter';
        document.getElementById('userRole').value = '';
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userModal').classList.remove('flex');
    }

    function editUser(id) {
        fetch(`/user/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Pengguna';
                document.getElementById('userForm').action = `/user/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('userId').value = id;
                document.getElementById('userName').value = data.name;
                document.getElementById('userUsername').value = data.username;
                document.getElementById('userPassword').value = '';
                document.getElementById('userPassword').required = false;
                document.getElementById('passRequired').style.display = 'none';
                document.getElementById('passHelp').textContent = 'Kosongkan jika tidak ingin mengubah password';
                document.getElementById('userRole').value = data.role;
                document.getElementById('userModal').classList.remove('hidden');
                document.getElementById('userModal').classList.add('flex');
            });
    }
</script>
@endpush
@endsection
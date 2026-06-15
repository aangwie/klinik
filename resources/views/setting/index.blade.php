@extends('layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan Website</h2>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('setting.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Nama Website -->
            <div class="mb-6">
                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Website</label>
                <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $appName) }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                    placeholder="Nama klinik / praktik">
            </div>

            <!-- Logo -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Website</label>
                <p class="text-xs text-gray-400 mb-3">Format: JPG, PNG, GIF, SVG. Maksimal 500KB. Logo akan digunakan di favicon dan struk pembayaran.</p>

                <div class="flex items-center gap-6">
                    <!-- Preview Logo -->
                    <div class="flex-shrink-0">
                        <div id="logoPreview" class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300 overflow-hidden">
                            @if($appLogo)
                            <img src="{{ \App\Models\Setting::getAppLogoBase64() }}" alt="Logo" class="w-full h-full object-contain p-1">
                            @else
                            <span class="text-2xl font-bold text-emerald-600">K</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1">
                        <label class="relative cursor-pointer bg-white rounded-lg border border-gray-300 px-4 py-2.5 hover:bg-gray-50 transition-colors inline-block">
                            <span class="text-sm font-medium text-gray-700">Pilih Gambar</span>
                            <input type="file" name="app_logo" id="app_logo" class="sr-only" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml">
                        </label>
                        <p id="fileName" class="text-xs text-gray-400 mt-1"></p>

                        @if($appLogo)
                        <label class="inline-flex items-center gap-2 mt-2 cursor-pointer text-sm text-red-600 hover:text-red-700">
                            <input type="checkbox" name="remove_logo" value="1">
                            <span>Hapus logo saat ini</span>
                        </label>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Pratinjau</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($appLogo)
                        <img src="{{ \App\Models\Setting::getAppLogoBase64() }}" alt="" class="w-full h-full object-contain">
                        @else
                        <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">K</div>
                        @endif
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-800 preview-name">{{ $appName }}</span>
                        <p class="text-xs text-gray-400">Praktik Dokter Umum</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview logo when file selected
    document.getElementById('app_logo')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (max 500KB = 512000 bytes)
            if (file.size > 512 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 500KB.');
                this.value = '';
                return;
            }

            document.getElementById('fileName').textContent = file.name;

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo" class="w-full h-full object-contain p-1">';
            };
            reader.readAsDataURL(file);
        }
    });

    // Live preview of app name
    document.getElementById('app_name')?.addEventListener('input', function() {
        document.querySelector('.preview-name').textContent = this.value || 'Klinik Sehat';
    });
</script>
@endpush
@endsection
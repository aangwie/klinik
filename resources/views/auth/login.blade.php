@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-emerald-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl shadow-emerald-100/50 p-8 mx-4">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4">K</div>
                <h2 class="text-2xl font-bold text-gray-800">Klinik Sehat</h2>
                <p class="text-gray-500 mt-1">Praktik Dokter Umum</p>
            </div>

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-3 mb-4 text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200 @error('username') border-red-300 @enderror"
                        placeholder="Masukkan username">
                    @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordField" required
                            class="w-full px-4 py-2.5 pr-12 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200 @error('password') border-red-300 @enderror"
                            placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <!-- Eye closed (password hidden) -->
                            <svg id="eyeClosed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye open (password visible) -->
                            <svg id="eyeOpen" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 shadow-lg shadow-emerald-200 hover:shadow-emerald-300">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeClosed = document.getElementById('eyeClosed');
        const eyeOpen = document.getElementById('eyeOpen');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeClosed.classList.add('hidden');
            eyeOpen.classList.remove('hidden');
        } else {
            passwordField.type = 'password';
            eyeClosed.classList.remove('hidden');
            eyeOpen.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
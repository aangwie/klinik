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
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all duration-200 @error('password') border-red-300 @enderror"
                        placeholder="Masukkan password">
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
@endsection
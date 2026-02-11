{{-- Logic Otomatis Pilih Layout --}}
@extends(Auth::user()->role === 'patient' ? 'layouts.patient' : 'layouts.app')

@section('content')
@php
$role = Auth::user()->role ?? 'patient';
$theme = [
'admin' => ['color' => 'indigo', 'bg' => 'bg-indigo-600', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50', 'icon' => 'fa-user-shield', 'label' => 'Administrator'],
'nakes' => ['color' => 'blue', 'bg' => 'bg-blue-600', 'text' => 'text-blue-600', 'light' => 'bg-blue-50', 'icon' => 'fa-user-md', 'label' => 'Tenaga Kesehatan'],
'patient' => ['color' => 'sky', 'bg' => 'bg-sky-500', 'text' => 'text-sky-500', 'light' => 'bg-sky-50', 'icon' => 'fa-hospital-user', 'label' => 'Pasien Bunda'],
][$role];

// Cek apakah menggunakan layout admin agar padding atas disesuaikan (karena fixed navbar)
$isStaff = in_array($role, ['admin', 'nakes']);
@endphp

<div class="max-w-5xl mx-auto p-4 md:p-10 pb-24"
    x-data="{ openEdit: false, openPassword: false }">

    {{-- Header Profile Card --}}
    <div class="relative bg-white rounded-[2rem] md:rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-8">
        {{-- Profile Cover --}}
        <div class="h-32 md:h-48 {{ $theme['bg'] }} opacity-90 relative">
            <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
            {{-- Badge Role (Hanya Muncul di Layar Lebar) --}}
            <div class="absolute top-6 right-8 hidden md:block">
                <div class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-2xl text-white text-[10px] font-black uppercase tracking-widest border border-white/30">
                    Sistem Lia's Dent v1.0
                </div>
            </div>
        </div>

        <div class="px-6 md:px-12 pb-10 -mt-16 md:-mt-24 relative z-10">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 text-center md:text-left">
                {{-- Avatar --}}
                <div class="relative group">
                    <div class="w-32 h-32 md:w-44 md:h-44 rounded-[2.5rem] bg-white p-2 shadow-2xl transition-transform group-hover:scale-105 duration-500">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ str_replace('bg-', '', $theme['bg']) }}&color=fff&size=200"
                            class="w-full h-full rounded-[2rem] object-cover" alt="Profile">
                    </div>
                </div>

                {{-- User Info --}}
                <div class="flex-1 pb-2">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 {{ $theme['light'] }} {{ $theme['text'] }} text-[10px] font-black rounded-xl uppercase tracking-widest mb-3">
                        <i class="fas {{ $theme['icon'] }}"></i>
                        {{ $theme['label'] }}
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-gray-400 font-medium flex items-center justify-center md:justify-start gap-2">
                        <i class="fa-regular fa-envelope"></i> {{ $user->email }}
                    </p>
                </div>

                {{-- Action Button --}}
                <div class="flex gap-2">
                    <button @click="openEdit = true" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs tracking-widest shadow-lg hover:shadow-gray-300 hover:-translate-y-1 transition-all active:scale-95">
                        EDIT PROFIL
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Content --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Data Personal --}}
        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter mb-8 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl {{ $theme['light'] }} {{ $theme['text'] }} flex items-center justify-center">
                    <i class="fa-solid fa-address-card text-sm"></i>
                </span>
                Informasi Akun
            </h3>

            <div class="space-y-6">
                <div class="group">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1 group-hover:{{ $theme['text'] }} transition-colors">Nama Lengkap</label>
                    <p class="font-bold text-gray-700 text-lg">{{ $user->name }}</p>
                </div>
                <div class="group">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1 group-hover:{{ $theme['text'] }} transition-colors">Alamat Email</label>
                    <p class="font-bold text-gray-700 text-lg">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status Keanggotaan</label>
                    <span class="inline-block px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg uppercase">Aktif</span>
                </div>
            </div>
        </div>

        {{-- Keamanan --}}
        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col transition-all hover:shadow-md">
            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter mb-8 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </span>
                Pusat Keamanan
            </h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                Amankan akun Anda dengan memperbarui kata sandi secara berkala. Pastikan menggunakan kombinasi karakter yang kuat.
            </p>

            <div class="mt-auto space-y-3">
                <button @click="openPassword = true" class="w-full py-4 bg-gray-50 text-gray-700 rounded-2xl font-black text-[10px] tracking-widest hover:bg-gray-100 transition-all uppercase flex items-center justify-center gap-2">
                    <i class="fa-solid fa-key text-xs opacity-50"></i> Ganti Password
                </button>
                <form method="post" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-4 text-red-400 font-black text-[10px] tracking-widest uppercase hover:text-red-600 transition-colors">
                        Logout dari Sesi
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PROFIL --}}
    <template x-teleport="body">
        <div x-show="openEdit" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div x-show="openEdit" x-transition.opacity @click="openEdit = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            <div x-show="openEdit" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95 translate-y-8" class="relative w-full max-w-lg bg-white p-8 md:p-12 rounded-[3.5rem] shadow-2xl overflow-hidden">
                {{-- Decorative Circle --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 {{ $theme['bg'] }} opacity-5 rounded-full"></div>

                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter mb-8">Update Profil</h2>
                <form method="post" action="{{ route('profile.update') }}" class="space-y-6 relative">
                    @csrf
                    @method('patch')
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-{{ $theme['color'] }}-400 font-bold transition-all" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-{{ $theme['color'] }}-400 font-bold transition-all" required>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="openEdit = false" class="flex-1 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black text-xs tracking-widest hover:bg-gray-200 transition-colors uppercase">Batal</button>
                        <button type="submit" class="flex-1 py-4 {{ $theme['bg'] }} text-white rounded-2xl font-black text-xs tracking-widest shadow-lg shadow-{{ $theme['color'] }}-100 hover:brightness-110 transition-all uppercase">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL GANTI PASSWORD --}}
    <template x-teleport="body">
        <div x-show="openPassword" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div x-show="openPassword" x-transition.opacity @click="openPassword = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            <div x-show="openPassword" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95 translate-y-8" class="relative w-full max-w-lg bg-white p-8 md:p-12 rounded-[3.5rem] shadow-2xl">
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter mb-8">Keamanan Akun</h2>
                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Password Saat Ini</label>
                        <input type="password" name="current_password" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-red-400 font-bold" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Password Baru</label>
                        <input type="password" name="password" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-red-400 font-bold" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-red-400 font-bold" placeholder="••••••••">
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="openPassword = false" class="flex-1 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black text-xs tracking-widest hover:bg-gray-200 transition-colors uppercase">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs tracking-widest shadow-lg hover:bg-black transition-all uppercase">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- Notifikasi Toast --}}
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[110] bg-gray-900 text-white px-8 py-4 rounded-2xl font-bold shadow-2xl flex items-center gap-3">
        <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-[10px]">✓</span>
        Berhasil diperbarui!
    </div>
    @endif
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    input:focus {
        outline: none !important;
    }
</style>
@endsection

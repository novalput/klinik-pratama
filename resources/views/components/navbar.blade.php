<div x-data="{ mobileMenuOpen: false }">

    <nav class="bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-[60] px-4 md:px-8 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            {{-- Tombol Hamburger --}}
            <div class="md:hidden">
                <button @click.stop="mobileMenuOpen = true" class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-500 hover:bg-gray-50 active:scale-95 transition-all">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="font-sansita text-2xl text-[#5DB8D8] tracking-tight">
                Klinik Pratama
            </a>
        </div>

        {{-- Desktop Menu --}}
        <div class="hidden md:flex items-center space-x-1 text-sm font-bold">
            <a href="{{ route('patient.dashboard') }}" class="px-4 py-2 rounded-xl {{ Request::routeIs('patient.dashboard') ? 'bg-sky-50 text-[#5DB8D8]' : 'text-gray-500' }}">Beranda</a>
            <a href="{{ route('patient.monitoring.index') }}" class="px-4 py-2 rounded-xl {{ Request::routeIs('patient.monitoring.*') ? 'bg-sky-50 text-[#5DB8D8]' : 'text-gray-500' }}">Kegiatan</a>
            <a href="{{ route('patient.education.index') }}" class="px-4 py-2 rounded-xl {{ Request::routeIs('patient.education.*') ? 'bg-sky-50 text-[#5DB8D8]' : 'text-gray-500' }}">Edukasi</a>
        </div>

        {{-- Profile Dropdown --}}
        <div class="flex items-center gap-3" x-data="{ userDropdown: false }">
            <div class="relative">
                <button @click="userDropdown = !userDropdown" @click.away="userDropdown = false" class="flex items-center focus:outline-none group">
                    <div class="p-0.5 rounded-full bg-gradient-to-tr from-[#5DB8D8] to-sky-200">
                        <img class="h-9 w-9 rounded-full border-2 border-white object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=5DB8D8&color=fff">
                    </div>
                </button>
                <div x-show="userDropdown" x-cloak class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-50 py-3 z-[100]">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-sky-50">
                        <i class="fa-solid fa-user-gear"></i> Pengaturan
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-500 font-bold hover:bg-red-50 flex items-center gap-3">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="md:hidden">
        {{-- Overlay: z-index 9998 --}}
        <div x-show="mobileMenuOpen"
            x-cloak
            x-transition:opacity
            @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-slate-900/60 z-[9998]"
            style="position: fixed !important;">
        </div>

        {{-- Sidebar: z-index 9999 --}}
        <aside x-show="mobileMenuOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed top-0 left-0 h-full w-80 bg-white shadow-2xl rounded-r-[2.5rem] flex flex-col"
            style="z-index: 9999 !important; position: fixed !important;">

            <div class="p-8 flex-1 overflow-y-auto">
                <div class="flex justify-between items-center mb-10">
                    <div class="font-sansita text-2xl text-[#5DB8D8]">Lia's Dent</div>
                    <button @click="mobileMenuOpen = false" class="w-10 h-10 flex items-center justify-center bg-gray-50 rounded-2xl text-gray-400">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <nav class="space-y-4">
                    <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::routeIs('patient.dashboard') ? 'bg-[#5DB8D8] text-white shadow-lg' : 'text-gray-500' }}">
                        <i class="fa-solid fa-home"></i> <span class="font-bold text-xs uppercase">Beranda</span>
                    </a>
                    <a href="{{ route('patient.education.index') }}" class="flex items-center gap-4 p-4 rounded-2xl {{ Request::routeIs('patient.education.*') ? 'bg-[#5DB8D8] text-white shadow-lg' : 'text-gray-500' }}">
                        <i class="fa-solid fa-book-open"></i> <span class="font-bold text-xs uppercase">Edukasi</span>
                    </a>
                </nav>
            </div>

            <div class="p-8 border-t border-gray-50 pb-[calc(2rem+env(safe-area-inset-bottom))]">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-4 w-full p-4 rounded-2xl text-red-400 font-bold text-xs uppercase">
                        <i class="fa-solid fa-power-off"></i> <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>

<aside
    x-show="sidebarOpen"
    {{-- (Animasi transition tetap sama) --}}
    class="fixed top-0 left-0 bottom-0 z-40 w-64 bg-green-700 text-white pt-20 pb-6 px-4 flex flex-col shadow-2xl overflow-y-auto no-scrollbar">

    <ul class="flex-1 space-y-2">

        {{-- Item: Dashboard --}}
        <li>
            @php
                $dashboardRoute = '#';
                if(Auth::user()->role === 'dokter') $dashboardRoute = route('dokter.dashboard');
                elseif(Auth::user()->role === 'nakes') $dashboardRoute = route('nakes.dashboard');
                elseif(Auth::user()->role === 'staf_administrasi') $dashboardRoute = route('staff_administrasi.dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all duration-200
            {{ request()->routeIs('*.dashboard*') ? 'bg-white text-[#5DB8D8] shadow-md' : 'hover:bg-white/20 hover:pl-6' }}">
                <i class="fa-solid fa-chart-line w-5 text-center"></i> Dashboard
            </a>
        </li>

        {{-- Item: Data Pasien dan Rekam Medis --}}
        {{-- UPDATE: Tambahkan staf_administrasi di kondisi ini --}}
        @if(in_array(Auth::user()->role, ['nakes', 'dokter', 'staf_administrasi']))
        <li>
            @php
                $pasienRoute = '#';
                if(Auth::user()->role === 'dokter') $pasienRoute = route('dokter.datapasien');
                elseif(Auth::user()->role === 'nakes') $pasienRoute = route('nakes.datapasien');
                elseif(Auth::user()->role === 'staf_administrasi') $pasienRoute = route('staff_administrasi.datapasien.index');
            @endphp

            <a href="{{ $pasienRoute }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all duration-200
            {{ request()->routeIs('*.datapasien*') || request()->routeIs('*.monitoring*') ? 'bg-white text-[#5DB8D8] shadow-md' : 'hover:bg-white/20 hover:pl-6' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> Data Pasien dan Rekam Medis
            </a>
        </li>
        @endif

        {{-- Item: Jadwal Dokter (Hanya Dokter) --}}
        @if(Auth::user()->role === 'dokter')
        <li>
            <a href="{{ route('dokter.schedule') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all duration-200
                {{ request()->routeIs('dokter.schedule') ? 'bg-white text-[#5DB8D8] shadow-md' : 'hover:bg-white/20 hover:pl-6' }}">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i> Jadwal
            </a>
        </li>
        @endif

    </ul>
    
    {{-- Logout Section --}}
    <div class="mt-6 pt-6 border-t border-white/30">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-red-500/80 hover:text-white font-bold transition-colors">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</aside>
{{-- Desktop Sidebar - Hidden on mobile, visible on md and up --}}
<aside
    id="sidebar-desktop"
    class="fixed inset-y-0 left-0 z-40 w-64
           bg-gradient-to-b from-blue-700 to-blue-900
           text-white shadow-2xl hidden md:flex flex-col">

    {{-- Logo / Title --}}
    <div class="flex items-center gap-3 px-6 py-8 border-b border-[#4da4e3] border-opacity-20 flex-shrink-0">
        <div class="bg-[#4da4e3] rounded-lg p-2">
            <i class="fas fa-hospital text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold">Klinik Pratama</h1>
            <p class="text-xs text-white-400">Admin Panel</p>
        </div>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.dashboard') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        
        {{-- Kelola Dokter --}}
        <a href="{{ route('admin.dokter.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.dokter.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-user-doctor w-5"></i>
            <span class="font-medium">Kelola Dokter</span>
        </a>

        {{-- Kelola Nakes --}}
        <a href="{{ route('admin.nakes.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.nakes.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-user-nurse w-5"></i>
            <span class="font-medium">Kelola Nakes</span>
        </a>

        {{-- Kelola Karyawan --}}
        <a href="{{ route('admin.workers.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.workers.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-user-nurse w-5"></i>
            <span class="font-medium">Kelola Karyawan</span>
        </a>
        
        {{-- Kelola Staff Administrasi --}}
        <a href="{{ route('admin.staff.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.staff.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-user-nurse w-5"></i>
            <span class="font-medium">Kelola Staff Admin</span>
        </a>

        {{-- Data Pasien --}}
        <a href="{{ route('admin.patient.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.patient.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-hospital-user w-5"></i>
            <span class="font-medium">Data Pasien</span>
        </a>
    </nav>

    {{-- Divider --}}
    <div class="px-4 py-4 border-t border-[#4da4e3] border-opacity-20 flex-shrink-0">
        {{-- User Info --}}
        <div class="mb-4 p-3 bg-[#4da4e3] bg-opacity-10 rounded-lg">
            <p class="text-xs text-gray-400 mb-1">Logged in as</p>
            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile Sidebar Overlay --}}
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
    x-cloak>
</div>

{{-- Mobile Sidebar - Fixed overlay on mobile only --}}
<aside
    id="sidebar-mobile"
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-0 z-50 md:hidden flex flex-col w-64 bg-gradient-to-b from-blue-700 to-blue-900 text-white shadow-2xl"
    x-cloak>

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-[#4da4e3] border-opacity-20 flex-shrink-0">
        <div class="flex items-center gap-2">
            <div class="bg-[#4da4e3] rounded-lg p-2">
                <i class="fas fa-tooth text-white text-lg"></i>
            </div>
            <h1 class="text-lg font-bold">Klinik Pratama</h1>
        </div>
        <button @click="sidebarOpen = false" class="text-gray-300 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('dashboard') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        {{-- Kelola Nakes --}}
        <a href="{{ route('admin.nakes.index') }}"
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.nakes.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-users-between-lines w-5"></i>
            <span class="font-medium">Kelola Nakes</span>
        </a>

        {{-- Data Pasien --}}
        <a href="{{ route('admin.patient.index') }}"
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.patient.*') ? 'bg-[#4da4e3] text-white shadow-lg' : 'text-gray-300 hover:bg-[#4da4e3]/20 hover:text-white' }}">
            <i class="fas fa-hospital-user w-5"></i>
            <span class="font-medium">Data Pasien</span>
        </a>
    </nav>

    {{-- Divider --}}
    <div class="px-4 py-4 border-t border-[#4da4e3] border-opacity-20 flex-shrink-0">
        {{-- User Info --}}
        <div class="mb-4 p-3 bg-[#4da4e3] bg-opacity-10 rounded-lg">
            <p class="text-xs text-gray-400 mb-1">Logged in as</p>
            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile Toggle Button (FAB) --}}
<button
    @click="sidebarOpen = !sidebarOpen"
    class="fixed bottom-6 right-6 z-30 md:hidden bg-[#4da4e3] text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-200 hover:scale-110"
    title="Toggle Menu">
    <i class="fas fa-bars text-lg"></i>
</button>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Klinik Pratama | @yield('title', "Klinik Pratama")</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Sansita+One&display=swap" rel="stylesheet">

    {{-- Icons & Assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .font-sansita {
            font-family: 'Sansita One', cursive;
        }

        body {
            font-family: 'Quicksand', sans-serif;
        }

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .modal * {
            box-sizing: border-box;
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>

<body
    class="{{ auth()->check() && auth()->user()->role === 'patient' ? 'h-full bg-[#FBFEF9]' : 'bg-gray-100' }} text-gray-900 antialiased overflow-x-hidden"
    x-data="{
        {{-- Jika Admin/Nakes default buka, jika Patient default tutup --}}
        sidebarOpen: {{ auth()->user()->role !== 'patient' ? 'true' : 'false' }},
        mobileMenuOpen: false
    }">

    {{-- Navbar --}}
    @auth
    @if(auth()->user()->role === 'nakes' || auth()->user()->role === 'dokter' || auth()->user()->role === 'staf_administrasi')
    @include('layouts.navigation-auth')
    @elseif(auth()->user()->role === 'patient')
    @include('components.navbar')
    @endif
    @endauth

    {{-- Sidebar --}}
    @auth
    @if(auth()->user()->role === 'admin')
    @include('layouts.sidebar_admin')
    @elseif(auth()->user()->role === 'nakes' || auth()->user()->role === 'dokter' || auth()->user()->role === 'staf_administrasi')
    @include('layouts.sidebar')
    @endif
    @endauth

    {{-- WRAPPER KANAN --}}
    <div
        class="h-screen flex flex-col transition-all duration-300 max-w-full"
        :class="sidebarOpen && '{{ auth()->user()->role }}' !== 'patient' ? 'md:ml-64' : 'md:ml-0'">

        {{-- SLOT KONTEN --}}
        <main class="{{ 
            auth()->check() && in_array(auth()->user()->role, ['role1', 'role2', 'role3']) 
                ? 'flex-1 pt-20 pb-8 px-4 md:px-8' 
                : (auth()->user()->role === 'patient' 
                    ? 'grow' 
                    : (auth()->user()->role === 'admin' 
                        ? 'flex-1 pt-8 pb-8 px-4 md:px-8' 
                        : 'flex-1 pt-20 pb-8 px-4 md:px-8'
                    )
                ) 
        }}">
            @yield('content')
        </main>   

        {{-- FOOTER --}}
        <!-- @auth
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'nakes')
        @include('components.admin.footer')
        @elseif(auth()->user()->role === 'patient')
        @include('components.footer')
        @endif
        @endauth -->
    </div>

    @stack('scripts')
    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('div.flex-1');

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('w-16');
            content.classList.toggle('md:ml-16');
        });
    </script>
</body>

</html>
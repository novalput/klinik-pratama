<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Web Klinik Pratama | @yield('title', "Web Klinik Pratama")</title>

    {{-- Fonts: Quicksand untuk kenyamanan & Sansita untuk Judul --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Sansita+One&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            scroll-behavior: smooth;
        }

        .font-sansita {
            font-family: 'Sansita One', cursive;
        }

        /* Custom Soft Scrollbar untuk area edukasi */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-[#FBFEF9] text-slate-900 antialiased min-h-full flex flex-col overflow-x-hidden">

    {{-- Navbar Khusus Patient --}}
    @include('components.navbar')

    {{-- Content Wrapper --}}
    {{-- Kita hilangkan logic sidebarOpen karena Patient tidak butuh sidebar admin --}}
    <main class="flex-grow">
        {{-- Area untuk Flash Message (Sukses/Error) --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="fixed top-24 right-4 z-[100] bg-white border-l-4 border-emerald-500 shadow-2xl p-4 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <div class="bg-emerald-100 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-check"></i>
            </div>
            <p class="text-sm font-bold text-slate-700">{{ session('success') }}</p>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer Khusus Patient --}}
    @include('components.footer')

    @stack('scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitur Sedang Dioptimalisasi | Sehat Bumil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="h-full antialiased text-slate-600 bg-[radial-gradient(circle_at_top_right,_#fff5f7_0%,_#f0f9ff_100%)]">

    <div class="flex min-h-full items-center justify-center p-6">
        <div class="relative w-full max-w-lg">

            <div class="absolute -top-12 -left-12 h-32 w-32 rounded-full bg-pink-100/50 blur-2xl"></div>
            <div class="absolute -bottom-12 -right-12 h-32 w-32 rounded-full bg-teal-100/50 blur-2xl"></div>

            <main
                class="relative overflow-hidden rounded-[2.5rem] border border-white/40 bg-white/70 p-8 shadow-2xl shadow-pink-200/30 backdrop-blur-xl md:p-12 text-center">

                <div
                    class="inline-flex items-center rounded-full bg-teal-50 px-4 py-1.5 text-xs font-bold text-teal-700 ring-1 ring-inset ring-teal-200/50 mb-8">
                    <span class="mr-2 h-2 w-2 rounded-full bg-teal-500"></span>
                    SEDANG DIPERBARUI
                </div>

                <div
                    class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-pink-50 md:h-28 md:w-28 shadow-inner">
                    <span class="text-5xl md:text-6xl">✨</span>
                </div>

                <h1 class="mb-4 text-2xl font-bold tracking-tight text-slate-800 md:text-3xl">
                    Halaman Sedang Dioptimalisasi
                </h1>

                <p class="mb-8 text-base leading-relaxed text-slate-500 md:px-4">
                    Mohon maaf Bunda, fitur ini sedang kami tingkatkan kualitasnya agar Bunda mendapatkan pengalaman
                    yang lebih nyaman dan lancar saat menggunakannya nanti.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex justify-center items-center rounded-full bg-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-pink-200 hover:bg-pink-500 transition-all active:scale-95">
                        Kembali ke Dashboard
                    </a>
                    <button onclick="history.back()"
                        class="inline-flex justify-center items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-all active:scale-95">
                        Halaman Sebelumnya
                    </button>
                </div>

                <div class="mt-10 border-t border-slate-100 pt-8 text-sm text-slate-400">
                    <p class="italic">Terima kasih atas pengertian Bunda.</p>
                    <div class="mt-4 flex justify-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-pink-200"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-200"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-pink-200"></span>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>

</html>

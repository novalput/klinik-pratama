<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="logo-font text-6xl md:text-7xl text-gray-800">{{ config('app.name', "Lia's Dent") }}</h1>
        <p class="text-gray-700 font-semibold mt-2">Monitoring Pasien</p>
    </div>

    <div class="auth-glass rounded-3xl shadow-2xl p-8 border border-white/20">

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.whatsapp') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 font-bold mb-1 ml-1" for="phone">Nomor WhatsApp</label>
                <div class="relative">
                    <input
                        type="number"
                        id="phone"
                        name="phone"
                        required
                        class="w-full px-5 py-3 pl-12 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#25D366] focus:border-transparent transition outline-none"
                        placeholder="08123456789">
                    <i class="fa-brands fa-whatsapp absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                </div>
                <p class="text-xs text-gray-500 mt-1 ml-1">Pastikan nomor aktif dan terdaftar.</p>
            </div>

            <button
                type="submit"
                class="w-full bg-[#25D366] hover:bg-[#20ba59] text-white font-bold py-3 rounded-2xl shadow-lg transition transform hover:scale-[1.02] active:scale-95 text-lg">
                Kirim Kode OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-[#5DB8D8] font-semibold text-sm transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login Email
            </a>
        </div>
    </div>
</x-guest-layout>

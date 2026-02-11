<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="logo-font text-6xl md:text-7xl text-gray-800">{{ config('app.name', "Lia's Dent") }}</h1>
        <p class="text-gray-700 font-semibold mt-2">Monitoring Pasien</p>
    </div>

    <div class="auth-glass rounded-3xl shadow-2xl p-8 border border-white/20">

        @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
            <br>
            <span class="text-xs text-gray-500">(Cek log laravel Anda untuk kode dummy)</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.whatsapp.check') }}" class="space-y-5">
            @csrf

            <div class="text-center">
                <label class="block text-gray-700 font-bold mb-3" for="otp">Kode 6 Digit</label>
                <div class="flex justify-center gap-2">
                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        maxlength="6"
                        required
                        class="w-full text-center tracking-[0.5em] text-2xl font-bold px-5 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-[#5DB8D8] focus:border-transparent transition outline-none"
                        placeholder="• • • • • •">
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-[#5DB8D8] hover:bg-[#4da7c7] text-white font-bold py-3 rounded-2xl shadow-lg transition transform hover:scale-[1.02] active:scale-95 text-lg">
                Verifikasi Masuk
            </button>
        </form>

        <div class="mt-6 text-center">
            <form action="{{ route('login.whatsapp.send') }}" method="POST">
                @csrf
                <input type="hidden" name="phone" value="{{ session('wa_phone') }}">
                <button type="submit" class="text-sm text-gray-600 hover:text-[#5DB8D8] underline bg-transparent border-none cursor-pointer">
                    Kirim ulang kode
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

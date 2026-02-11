<x-guest-layout>
    <div class="auth-glass rounded-3xl shadow-2xl p-8 border border-white/20" x-data="{ showPass: false }">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Setel Ulang Password</h2>
        <p class="text-center text-sm text-gray-600 mb-6">Masukkan kode OTP yang dikirim ke {{ session('reset_destination') }}</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-1">Kode OTP 6-Digit</label>
                <input type="text" name="otp" maxlength="6" required class="w-full text-center tracking-widest text-xl font-bold px-5 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-sky-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Password Baru</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required class="w-full px-5 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-sky-400 outline-none">
                    <button type="button" @click="showPass = !showPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Konfirmasi Password</label>
                <input :type="showPass ? 'text' : 'password'" name="password_confirmation" required class="w-full px-5 py-3 rounded-2xl border border-gray-300 focus:ring-2 focus:ring-sky-400 outline-none">
            </div>

            <button type="submit" class="w-full bg-sky-500 text-white font-bold py-3 rounded-2xl shadow-lg hover:bg-sky-600 transition">
                Perbarui Password
            </button>
        </form>
    </div>
</x-guest-layout>

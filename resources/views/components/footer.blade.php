<footer class="relative overflow-hidden bg-white pt-16 pb-8 text-slate-600 border-t border-sky-100">

    {{-- Aksen Dekorasi Medis (Menyesuaikan dengan Section About/Edukasi) --}}
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#5DB8D8] via-sky-300 to-[#5DB8D8] opacity-60"></div>

    {{-- Background Glow (Mengikuti style Section Education) --}}
    <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-sky-50 blur-3xl opacity-80"></div>
    <div class="absolute bottom-0 -left-24 h-64 w-64 rounded-full bg-slate-50 blur-3xl opacity-80"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 text-center lg:text-left">

            {{-- Kolom 1: Branding (Warna Hero/Header) --}}
            <div class="space-y-6">
                <div>
                    <h2 class="font-sansita text-4xl tracking-tight text-slate-800">Lia's Dent</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-500 max-w-xs mx-auto lg:mx-0">
                        Pendamping kesehatan gigi yang aman dan nyaman selama kehamilan. Membantu Bunda menjaga senyum sehat untuk Si Kecil.
                    </p>
                </div>

                <div class="flex justify-center lg:justify-start gap-4">
                    {{-- Social Media menggunakan warna #5DB8D8 dari Feature Cards --}}
                    @php $socials = ['instagram', 'youtube', 'x-twitter', 'tiktok']; @endphp
                    @foreach($socials as $icon)
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#EAF6F9] text-[#5DB8D8] hover:bg-[#5DB8D8] hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fab fa-{{ $icon }} text-lg"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Kolom 2: Kontak (Warna Sky Blue Medis) --}}
            <div class="space-y-6 lg:pl-12 border-l-0 lg:border-l lg:border-slate-100">
                <h5 class="text-xs font-bold uppercase tracking-[0.2em] text-[#5FAFC1] flex items-center justify-center lg:justify-start gap-2">
                    <i class="fas fa-clinic-medical"></i> LAYANAN BUNDA
                </h5>
                <ul class="space-y-4">
                    <li class="flex items-center justify-center lg:justify-start gap-4">
                        <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#F3FBFD] text-[#5DB8D8]">
                            <i class="fas fa-phone-alt text-sm"></i>
                        </div>
                        <span class="text-slate-700 font-medium text-sm">(+62) 821-1712-7459</span>
                    </li>
                    <li class="flex items-center justify-center lg:justify-start gap-4">
                        <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#F3FBFD] text-[#5DB8D8]">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <span class="text-slate-700 font-medium text-sm">meliacrisanth28@gmail.com</span>
                    </li>
                    <li class="flex items-center justify-center lg:justify-start gap-4">
                        <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#F3FBFD] text-[#5DB8D8]">
                            <i class="fas fa-comment-dots text-sm"></i>
                        </div>
                        <a href="#" class="text-slate-700 font-medium text-sm hover:text-[#5DB8D8] transition-colors">
                            Kirim Feedback & Saran
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Kolom 3: Status Sistem (Warna Dark Sky/Indigo dari CTA Section) --}}
            <div class="space-y-6 lg:pl-12">
                <h5 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-800 flex items-center justify-center lg:justify-start gap-2">
                    <i class="fas fa-shield-alt"></i> KEAMANAN DATA
                </h5>
                <p class="text-sm italic text-slate-400 leading-relaxed">
                    "Informasi Bunda bersifat rahasia dan digunakan untuk mendukung pelayanan kesehatan selama kehamilan."
                </p>

                {{-- Badge Status Aktif sesuai warna modal --}}
                <div class="inline-flex items-center gap-3 px-4 py-2 bg-[#F8FCFE] rounded-xl border border-sky-100">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#5DB8D8]"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Monitoring System Aktif</span>
                </div>
            </div>

        </div>

        {{-- Bottom Copyright (Sesuai warna text footer home) --}}
        <div class="mt-16 pt-8 border-t border-slate-100 text-center">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} Lia's Dent Monitoring
                <span class="mx-2 text-sky-200">|</span>
                Dibuat dengan <i class="fas fa-heart text-[#5DB8D8] mx-1"></i> untuk Bunda Sehat.
            </p>
        </div>
    </div>
</footer>

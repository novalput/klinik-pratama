@extends('layouts.app')

@section('content')
<div class="py-10 px-8 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-sansita text-gray-800">Daftar Feedback Pasien</h1>
        <p class="text-gray-500 italic text-sm">Ulasan dan saran dari Bunda untuk peningkatan layanan.</p>
    </div>

    <div class="grid gap-6">
        @forelse($feedbacks as $fb)
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col md:flex-row gap-5 transition-all hover:shadow-md">

            {{-- Foto & Nama Pasien --}}
            <div class="flex md:flex-col items-center gap-3 md:w-32 flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($fb->user->name) }}&background=5DB8D8&color=fff"
                    class="w-14 h-14 rounded-2xl shadow-sm">
                <div class="text-left md:text-center">
                    <h4 class="font-bold text-gray-800 text-sm leading-tight">{{ $fb->user->name }}</h4>
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Pasien</span>
                </div>
            </div>

            {{-- Isi Feedback --}}
            <div class="flex-1 flex flex-col border-t md:border-t-0 md:border-l border-gray-50 pt-4 md:pt-0 md:pl-6">
                <div class="flex justify-between items-center mb-3">
                    {{-- Tampilan Bintang Otomatis --}}
                    <div class="flex items-center gap-1 text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <=$fb->rating)
                            <i class="fa-solid fa-star text-sm"></i>
                            @else
                            <i class="fa-regular fa-star text-sm text-gray-200"></i>
                            @endif
                            @endfor
                            <span class="ml-2 text-gray-400 text-xs font-bold">({{ $fb->rating ?? 0 }}/5)</span>
                    </div>

                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider bg-gray-50 px-3 py-1 rounded-full">
                        {{ $fb->created_at->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>

                <div class="bg-sky-50/30 rounded-2xl p-4 relative">
                    <i class="fa-solid fa-quote-left absolute -top-2 -left-1 text-sky-100 text-2xl"></i>
                    <p class="text-sm text-gray-600 leading-relaxed italic relative z-10">
                        "{{ $fb->content }}"
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[2rem] p-12 text-center border-2 border-dashed border-gray-100">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
                <i class="fa-solid fa-comment-slash text-2xl"></i>
            </div>
            <p class="text-gray-400 font-medium">Belum ada feedback yang masuk dari pasien.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $feedbacks->links() }}
    </div>
</div>
@endsection

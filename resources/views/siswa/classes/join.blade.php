@extends('layouts.app')
@section('title', 'Bergabung ke Kelas')

@section('content')
<div class="max-w-lg lg:max-w-4xl mx-auto space-y-4 md:space-y-6 px-1 sm:px-0">

  <div class="flex items-center gap-2.5 md:gap-3">
    <a href="{{ route('siswa.classes.index') }}"
       class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-full bg-white border border-gray-200
              text-gray-400 hover:text-[#4F46E5] hover:border-indigo-300 transition flex-shrink-0">
      <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <div class="min-w-0">
      <h1 class="text-lg md:text-2xl font-bold text-gray-900 truncate">Bergabung ke Kelas 🚀</h1>
      <p class="text-[11px] md:text-sm text-gray-400">Satu langkah lagi menuju kelas barumu</p>
    </div>
  </div>

  {{-- Desktop (lg+): dua card berdampingan. Mobile/tablet: numpuk vertikal --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 lg:items-stretch">

    {{-- Card: Input Kode Manual --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-white via-indigo-50/60 to-violet-50/50 rounded-2xl md:rounded-3xl border-2 border-indigo-100 p-5 md:p-7 lg:p-9 space-y-4 md:space-y-5 shadow-md lg:shadow-xl lg:shadow-indigo-100/70 flex flex-col">
      <div class="absolute -top-16 -right-16 w-32 h-32 md:w-40 md:h-40 lg:w-56 lg:h-56 bg-gradient-to-br from-indigo-300/50 to-violet-300/50 rounded-full blur-2xl"></div>
      <div class="absolute -bottom-10 -left-10 w-28 h-28 md:w-36 md:h-36 bg-gradient-to-br from-[#818CF8]/30 to-indigo-200/30 rounded-full blur-2xl"></div>

      <div class="relative text-center space-y-1">
        <div class="w-12 h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-xl md:rounded-2xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8]
                    flex items-center justify-center text-xl md:text-3xl lg:text-4xl shadow-lg shadow-indigo-300/70 mb-2 md:mb-3">
          🔑
        </div>
        <h2 class="font-bold text-gray-800 text-base md:text-lg lg:text-xl">Masukkan Kode Kelas</h2>
        <p class="text-xs md:text-sm text-gray-500">
          Format: <span class="font-mono font-bold text-[#4F46E5]">MAPEL-XXXX</span>
          <br class="sm:hidden">
          <span class="text-gray-400">(contoh: IPA-BWPE)</span>
        </p>
      </div>

      @if($errors->any())
        <div class="bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#7F1D1D] text-xs md:text-sm rounded-xl px-3 md:px-4 py-2.5 md:py-3 flex items-center gap-2">
          <span>⚠️</span> {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('siswa.classes.join.post') }}" class="relative space-y-3 md:space-y-4 flex-1 flex flex-col justify-center">
        @csrf
        <input
          type="text"
          name="code"
          value="{{ old('code', $code) }}"
          placeholder="IPA-BWPE"
          autofocus
          maxlength="20"
          autocomplete="off"
          oninput="this.value = this.value.toUpperCase()"
          class="w-full border-2 border-gray-200 rounded-xl md:rounded-2xl px-3 md:px-4 py-3 md:py-4 lg:py-5
                 text-center text-lg md:text-2xl lg:text-3xl font-mono font-bold uppercase tracking-[0.2em] md:tracking-[0.3em]
                 text-[#4F46E5] placeholder:text-gray-300 placeholder:tracking-[0.2em] md:placeholder:tracking-[0.3em]
                 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-[#4F46E5]
                 transition-all
                 @error('code') border-red-300 @enderror"
        />
        <button type="submit"
          class="w-full bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:from-[#4338CA] hover:to-[#6366F1]
                 text-white font-bold text-sm md:text-base lg:text-lg py-3 md:py-3.5 lg:py-4 rounded-xl md:rounded-2xl transition-all shadow-lg shadow-indigo-200
                 hover:shadow-indigo-300 hover:-translate-y-0.5 flex items-center justify-center gap-2">
          Gabung Sekarang
          <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </form>
    </div>

    {{-- Divider: horizontal di mobile, vertical "ATAU" di tengah pada desktop ditangani via urutan grid --}}
    <div class="flex lg:hidden items-center gap-3 col-span-1">
      <div class="flex-1 h-px bg-gray-200"></div>
      <span class="text-[10px] md:text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full">ATAU</span>
      <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    {{-- Card: Scan QR --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-white via-violet-50/60 to-indigo-50/50 rounded-2xl md:rounded-3xl border-2 border-violet-100 p-5 md:p-7 lg:p-9 text-center space-y-3 md:space-y-4 shadow-md lg:shadow-xl lg:shadow-violet-100/70 flex flex-col"
         x-data="qrScanner()" x-init="init()">
      <div class="absolute -bottom-16 -left-16 w-32 h-32 md:w-40 md:h-40 lg:w-56 lg:h-56 bg-gradient-to-br from-violet-300/50 to-indigo-300/50 rounded-full blur-2xl"></div>
      <div class="absolute -top-10 -right-10 w-28 h-28 md:w-36 md:h-36 bg-gradient-to-br from-indigo-200/30 to-violet-200/30 rounded-full blur-2xl"></div>

      <div class="relative">
        <div class="w-12 h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 mx-auto rounded-xl md:rounded-2xl bg-gradient-to-br from-violet-500 to-[#4F46E5]
                    flex items-center justify-center text-xl md:text-3xl lg:text-4xl shadow-lg shadow-violet-300/70 mb-2 md:mb-3">
          📷
        </div>
        <h2 class="font-bold text-gray-800 text-base md:text-lg lg:text-xl">Scan QR Code Kelas</h2>
        <p class="text-xs md:text-sm text-gray-500 px-2 md:px-0">Arahkan kamera ke QR Code yang ditampilkan gurumu</p>
      </div>

      {{-- Preview kamera --}}
      <div x-show="scanning" class="relative space-y-3 flex-1 flex flex-col items-center justify-center">
        <div class="relative w-48 md:w-64 lg:w-72 mx-auto rounded-2xl overflow-hidden border-4 border-[#4F46E5] bg-black aspect-square shadow-xl">
          <video id="qr-video" class="w-full h-full object-cover" playsinline></video>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-28 h-28 md:w-40 md:h-40 border-2 border-white/80 rounded-2xl relative">
              <div class="absolute -top-1 -left-1 w-5 h-5 md:w-6 md:h-6 border-t-4 border-l-4 border-[#818CF8] rounded-tl-lg"></div>
              <div class="absolute -top-1 -right-1 w-5 h-5 md:w-6 md:h-6 border-t-4 border-r-4 border-[#818CF8] rounded-tr-lg"></div>
              <div class="absolute -bottom-1 -left-1 w-5 h-5 md:w-6 md:h-6 border-b-4 border-l-4 border-[#818CF8] rounded-bl-lg"></div>
              <div class="absolute -bottom-1 -right-1 w-5 h-5 md:w-6 md:h-6 border-b-4 border-r-4 border-[#818CF8] rounded-br-lg"></div>
            </div>
          </div>
          <div class="absolute left-0 right-0 top-0 h-1 bg-[#818CF8] shadow-[0_0_10px_2px_rgba(129,140,248,0.8)] animate-[scan_2s_ease-in-out_infinite]"></div>
        </div>
        <button type="button" @click="stopScan()"
          class="text-xs md:text-sm text-red-500 hover:text-red-700 font-medium">
          ✕ Batal
        </button>
      </div>

      <div x-show="!scanning" class="flex-1 flex items-center justify-center">
        <button type="button" @click="startScan()"
          class="inline-flex items-center gap-2 bg-white border-2 border-violet-500 text-violet-600 hover:bg-violet-50 hover:shadow-lg hover:shadow-violet-200/60
                 font-semibold text-sm md:text-base lg:text-lg px-4 md:px-6 lg:px-8 py-2.5 md:py-3 lg:py-4 rounded-xl md:rounded-2xl transition-all hover:-translate-y-0.5">
          📷 Buka Kamera
        </button>
      </div>

      <p x-show="error" x-text="error" class="text-xs md:text-sm text-red-500 font-medium"></p>
    </div>
  </div>
</div>

<style>
@keyframes scan {
  0%, 100% { top: 0; }
  50% { top: calc(100% - 4px); }
}
</style>

<!-- jsQR: library decode QR dari kamera -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
function qrScanner() {
  return {
    scanning: false, error: '', stream: null, animFrame: null,
    init() {},
    async startScan() {
      this.error = '';
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' }
        });
        this.scanning = true;
        await this.$nextTick();
        const video = document.getElementById('qr-video');
        video.srcObject = this.stream;
        await video.play();
        this.tick(video);
      } catch (e) {
        this.error = 'Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.';
      }
    },
    tick(video) {
      if (!this.scanning) return;
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0);
      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const code = jsQR(imgData.data, imgData.width, imgData.height);
      if (code) {
        this.stopScan();
        try {
          const url = new URL(code.data);
          const kode = url.searchParams.get('code');
          if (kode) { window.location.href = url.href; }
          else { this.error = 'QR Code tidak valid.'; }
        } catch { this.error = 'QR Code tidak dikenali.'; }
        return;
      }
      this.animFrame = requestAnimationFrame(() => this.tick(video));
    },
    stopScan() {
      this.scanning = false;
      if (this.stream) {
        this.stream.getTracks().forEach(t => t.stop());
        this.stream = null;
      }
      if (this.animFrame) cancelAnimationFrame(this.animFrame);
    }
  }
}
</script>
@endsection
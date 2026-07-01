@extends('layouts.dashboard')

@section('title', 'Form Assessment Mamdani')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center gap-4 bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg">
        <a href="{{ route('staff.assessment') }}" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold">Penilaian Akhir AI Mamdani</h2>
            <p class="text-gray-400 text-sm">Review Data: <strong class="text-white">{{ $application->student->full_name }}</strong></p>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-4 flex items-start gap-3 shadow-lg">
        <i class="fas fa-robot text-blue-400 text-xl mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-blue-300">Tahap Eksekusi AI Mamdani</p>
            <p class="text-xs text-gray-400 mt-1">Seluruh berkas di bawah ini telah diverifikasi. Silakan klik tombol di bawah untuk mengeksekusi 72 Aturan Fuzzy Mamdani.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('staff.assessment.store', $application->application_id) }}" class="space-y-6">
        @csrf
        
        <!-- 1. CARD IPK -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-graduation-cap text-blue-400"></i> Data IPK Kumulatif</h3>
            @php $autoIpk = $application->student->semesterGpas->isNotEmpty() ? round($application->student->semesterGpas->avg('gpa'), 2) : 0; @endphp
            <div class="w-full px-4 py-4 bg-slate-800/50 border border-slate-700 rounded-lg flex justify-between items-center">
                <span class="text-sm font-medium text-gray-400">IPK Terverifikasi</span>
                <span class="text-2xl font-black text-blue-400">{{ $autoIpk }}</span>
            </div>
        </div>

        <!-- 2. CARD TANGGUNGAN -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-users text-purple-400"></i> Data Tanggungan Keluarga</h3>
            <div class="w-full px-4 py-4 bg-slate-800/50 border border-slate-700 rounded-lg flex justify-between items-center">
                <span class="text-sm font-medium text-gray-400">Tanggungan Terverifikasi</span>
                <span class="text-2xl font-black text-purple-400">{{ $application->student->parentGuardian->dependents_count ?? 0 }} <span class="text-sm font-normal text-gray-500">Orang</span></span>
            </div>
        </div>

        <!-- 3. CARD GAJI -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-money-bill-wave text-green-400"></i> Data Penghasilan Total</h3>
            @php $totalIncome = ($application->student->parentGuardian->father_income ?? 0) + ($application->student->parentGuardian->mother_income ?? 0); @endphp
            <div class="w-full px-4 py-4 bg-slate-800/50 border border-slate-700 rounded-lg flex justify-between items-center">
                <span class="text-sm font-medium text-gray-400">Total Penghasilan Terverifikasi</span>
                <span class="text-2xl font-black text-green-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 4. CARD RUMAH (READ ONLY) -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-home text-orange-400"></i> Keputusan Kondisi Rumah</h3>
            @php $fotoRumah = $application->documents->where('document_type', 'house_photo'); @endphp
            @if($fotoRumah->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                @foreach($fotoRumah as $foto)
                <div class="block relative border border-slate-700 rounded-xl overflow-hidden">
                    <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-24 object-cover" alt="Foto Rumah">
                </div>
                @endforeach
            </div>
            @endif
            <div class="w-full px-4 py-4 bg-slate-800/50 border border-slate-700 rounded-lg">
                <p class="text-sm font-medium text-gray-400 mb-1">Status Kelayakan (Dinilai oleh Staff saat Verifikasi):</p>
                @if($application->assessment)
                    <p class="text-xl font-bold {{ $application->assessment->house_condition_score == 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $application->assessment->house_condition_score == 0 ? 'Layak Mendapat Beasiswa (Kondisi Rumah Terlihat Kurang Mampu)' : 'Tidak Layak (Kondisi Rumah Terlihat Bagus/Mewah)' }}
                    </p>
                @else
                    <p class="text-xl font-bold text-yellow-500">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Pengajuan ini belum melewati tahap verifikasi rumah yang baru.
                    </p>
                @endif
            </div>
        </div>

        <!-- 5. CARD PRESTASI -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-trophy text-yellow-400"></i> Data Prestasi</h3>
            @php $certCount = $application->documents->where('document_type', 'achievement_certificate')->count(); @endphp
            <div class="w-full px-4 py-4 bg-slate-800/50 border border-slate-700 rounded-lg flex justify-between items-center">
                <span class="text-sm font-medium text-gray-400">Total Sertifikat Terverifikasi</span>
                <span class="text-2xl font-black text-yellow-400">{{ $certCount }} <span class="text-sm font-normal text-gray-500">File</span></span>
            </div>
        </div>

        <!-- Tombol Eksekusi Akhir -->
        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-900/50 transition-all flex items-center justify-center gap-2 text-lg">
            <i class="fas fa-microchip"></i> Jalankan Algoritma AI Mamdani
        </button>

    </form>
</div>
@endsection
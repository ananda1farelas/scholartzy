@extends('layouts.dashboard')

@section('title', 'Detail Hasil Assessment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.results') }}" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-2xl font-bold">Detail Hasil Assessment</h2>
    </div>

    <!-- Result Card -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 border border-slate-700 rounded-2xl p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold">{{ substr($result->assessment->scholarshipApplication->student->full_name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold">{{ $result->assessment->scholarshipApplication->student->full_name }}</h3>
                    <p class="text-gray-400">{{ $result->assessment->scholarshipApplication->student->student_number }} • {{ $result->assessment->scholarshipApplication->student->study_program }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-5xl font-bold {{ $result->eligibility_status === 'recommended' ? 'text-green-400' : 'text-red-400' }}">
                    {{ $result->eligibility_score }}
                </div>
                <p class="text-sm text-gray-500 mt-1">Skor Kelayakan</p>
            </div>
        </div>

        <div class="w-full h-3 bg-slate-700 rounded-full overflow-hidden mb-4">
            <div class="h-full rounded-full {{ $result->eligibility_score >= 60 ? 'bg-gradient-to-r from-green-500 to-emerald-400' : 'bg-gradient-to-r from-red-500 to-orange-400' }}" 
                 style="width: {{ $result->eligibility_score }}%"></div>
        </div>

        <div class="flex items-center justify-between">
            <span class="px-4 py-2 rounded-full text-sm font-medium {{ $result->eligibility_status === 'recommended' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                <i class="fas {{ $result->eligibility_status === 'recommended' ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                {{ $result->eligibility_status === 'recommended' ? 'Direkomendasikan Menerima Beasiswa' : 'Tidak Direkomendasikan' }}
            </span>
            <span class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i> {{ $result->generated_at->format('d F Y H:i') }}
            </span>
        </div>
    </div>

    <!-- Criteria Breakdown -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
            <i class="fas fa-list-ul text-blue-400"></i> Detail Kriteria Penilaian
        </h3>
        
        <div class="grid md:grid-cols-2 gap-4">
            <!-- IPK -->
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400"><i class="fas fa-star mr-2 text-yellow-400"></i>IPK</span>
                    <span class="font-bold">{{ $result->assessment->ipk_score }}</span>
                </div>
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-400" style="width: {{ ($result->assessment->ipk_score / 4) * 100 }}%"></div>
                </div>
            </div>

            <!-- Penghasilan -->
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400"><i class="fas fa-wallet mr-2 text-purple-400"></i>Penghasilan</span>
                    <span class="font-bold">Rp{{ number_format($result->assessment->total_family_income, 0, ',', '.') }}</span>
                </div>
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-400" style="width: {{ min(100, ($result->assessment->total_family_income / 10000000) * 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Semakin rendah penghasilan, semakin tinggi kelayakan</p>
            </div>

            <!-- Tanggungan -->
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400"><i class="fas fa-users mr-2 text-teal-400"></i>Tanggungan</span>
                    <span class="font-bold">{{ $result->assessment->dependents_count }} orang</span>
                </div>
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-teal-400" style="width: {{ min(100, ($result->assessment->dependents_count / 8) * 100) }}%"></div>
                </div>
            </div>

            <!-- Prestasi -->
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400"><i class="fas fa-trophy mr-2 text-pink-400"></i>Prestasi</span>
                    <span class="font-bold">{{ $result->assessment->achievement_score }}/100</span>
                </div>
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-pink-400
                                        style="width: {{ $result->assessment->achievement_score }}%"></div>
                </div>
            </div>

            <!-- Rumah -->
            <div class="bg-slate-800/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400"><i class="fas fa-home mr-2 text-orange-400"></i>Kondisi Rumah</span>
                    <span class="font-bold">{{ $result->assessment->house_condition_score }}/100</span>
                </div>
                <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-400" style="width: {{ $result->assessment->house_condition_score }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fuzzy Process Info -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-brain text-purple-400"></i> Proses Fuzzy Mamdani
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-slate-800/50 rounded-xl">
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-random text-blue-400"></i>
                </div>
                <p class="text-xs font-medium">Fuzzifikasi</p>
                <p class="text-[10px] text-gray-500 mt-1">Input → Himpunan Fuzzy</p>
            </div>
            <div class="text-center p-4 bg-slate-800/50 rounded-xl">
                <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-project-diagram text-purple-400"></i>
                </div>
                <p class="text-xs font-medium">Inferensi</p>
                <p class="text-[10px] text-gray-500 mt-1">Aturan IF-THEN</p>
            </div>
            <div class="text-center p-4 bg-slate-800/50 rounded-xl">
                <div class="w-10 h-10 bg-teal-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-layer-group text-teal-400"></i>
                </div>
                <p class="text-xs font-medium">Agregasi</p>
                <p class="text-[10px] text-gray-500 mt-1">Gabungan Output</p>
            </div>
            <div class="text-center p-4 bg-slate-800/50 rounded-xl">
                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-bullseye text-green-400"></i>
                </div>
                <p class="text-xs font-medium">Defuzzifikasi</p>
                <p class="text-[10px] text-gray-500 mt-1">Centroid → Skor</p>
            </div>
        </div>
    </div>

    <!-- Assessor Info -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Informasi Assessment</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Assessor</p>
                <p class="text-sm font-medium">{{ $result->assessment->staff->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Assessment</p>
                <p class="text-sm font-medium">{{ $result->assessment->assessment_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Hasil</p>
                <p class="text-sm font-medium">{{ $result->generated_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Dokumen Mahasiswa -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Dokumen Pengajuan</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($result->assessment->scholarshipApplication->documents as $doc)
                @php
                    $docLabels = [
                        'transcript' => ['Transkrip Nilai', 'fa-file-pdf', 'text-red-400'],
                        'family_card' => ['Kartu Keluarga', 'fa-file-pdf', 'text-red-400'],
                        'income_proof' => ['Bukti Penghasilan', 'fa-file-pdf', 'text-red-400'],
                        'house_photo' => ['Foto Rumah', 'fa-image', 'text-green-400'],
                        'achievement_certificate' => ['Sertifikat Prestasi', 'fa-trophy', 'text-yellow-400'],
                    ];
                    $info = $docLabels[$doc->document_type] ?? [$doc->document_type, 'fa-file', 'text-gray-400'];
                @endphp
                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" 
                   class="flex items-center gap-3 p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition border border-slate-700/50">
                    <i class="fas {{ $info[1] }} {{ $info[2] }} text-xl"></i>
                    <span class="text-sm font-medium">{{ $info[0] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
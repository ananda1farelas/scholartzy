@extends('layouts.dashboard')

@section('title', 'Form Assessment')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.assessment') }}" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold">Assessment Fuzzy Mamdani</h2>
            <p class="text-gray-500 text-sm">Mahasiswa: {{ $application->student->full_name }} ({{ $application->student->student_number }})</p>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <i class="fas fa-info-circle text-blue-400"></i>
            <p class="text-sm text-gray-400">
                Data otomatis: Total penghasilan = <strong class="text-white">Rp{{ number_format(($application->student->parentGuardian->father_income ?? 0) + ($application->student->parentGuardian->mother_income ?? 0) + ($application->student->parentGuardian->guardian_income ?? 0), 0, ',', '.') }}</strong>, 
                Tanggungan = <strong class="text-white">{{ $application->student->parentGuardian->dependents_count ?? 0 }} orang</strong>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('staff.assessment.store', $application->application_id) }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
        @csrf

        <!-- IPK (Auto dari sistem, editable) -->
        @php
            $semesterGpas = $application->student->semesterGpas()->orderBy('semester_number')->get();
            $autoIpk = $semesterGpas->isNotEmpty() ? round($semesterGpas->avg('gpa'), 2) : '';
        @endphp
        
        @if($semesterGpas->isNotEmpty())
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
            <h4 class="text-sm font-semibold mb-3 text-blue-400">IPK dari Mahasiswa</h4>
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($semesterGpas as $sg)
                <div class="px-2 py-1 bg-slate-800 rounded text-xs">
                    <span class="text-gray-500">Smt {{ $sg->semester_number }}:</span>
                    <span class="font-bold {{ $sg->gpa >= 3.0 ? 'text-green-400' : 'text-yellow-400' }}">{{ $sg->gpa }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-700/50">
                <span class="text-xs text-gray-500">IPK Kumulatif:</span>
                <span class="font-bold text-white">{{ $autoIpk }}</span>
            </div>
        </div>
        @endif

        <!-- IPK -->
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Indeks Prestasi Kumulatif (IPK)</span>
                <span class="text-xs text-gray-500">0.00 - 4.00</span>
            </label>
            <input type="number" name="ipk_score" step="0.01" min="0" max="4" 
                value="{{ old('ipk_score', $autoIpk) }}"
                readonly="{{ $semesterGpas->isNotEmpty() ? 'readonly' : '' }}"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" 
                placeholder="Contoh: 3.75" required>
            <p class="text-xs text-gray-500 mt-1">
                @if($semesterGpas->isNotEmpty())
                    Auto-terisi dari data mahasiswa. Edit jika perlu.
                @else
                    Semakin tinggi IPK, semakin baik nilai kelayakan.
                @endif
            </p>
        </div>

        <!-- Total Penghasilan (Manual Override) -->
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Total Penghasilan Keluarga (Rp)</span>
                <span class="text-xs text-gray-500">Per bulan</span>
            </label>
            <input type="number" name="total_family_income" min="0" 
                value="{{ old('total_family_income', ($application->student->parentGuardian->father_income ?? 0) + ($application->student->parentGuardian->mother_income ?? 0) + ($application->student->parentGuardian->guardian_income ?? 0)) }}"
                readonly="{{ $semesterGpas->isNotEmpty() ? 'readonly' : '' }}"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" 
                placeholder="Contoh: 2500000" required>
            <p class="text-xs text-gray-500 mt-1">Edit jika ada perbedaan dengan data di sistem.</p>
        </div>

        <!-- Jumlah Tanggungan -->
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Jumlah Tanggungan Keluarga</span>
                <span class="text-xs text-gray-500">Orang</span>
            </label>
            <input type="number" name="dependents_count" min="0" 
                value="{{ old('dependents_count', $application->student->parentGuardian->dependents_count ?? 0) }}" 
                readonly="{{ $semesterGpas->isNotEmpty() ? 'readonly' : '' }}"
                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" 
                required>
        </div>

        <!-- Prestasi -->
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Skor Prestasi Mahasiswa</span>
                <span class="text-xs text-gray-500">0 - 100</span>
            </label>
            <input type="range" name="achievement_score" min="0" max="100" value="{{ old('achievement_score', 50) }}" 
                class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500"
                oninput="document.getElementById('achievement-value').textContent = this.value" required>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>0 (Tidak ada)</span>
                <span id="achievement-value" class="text-blue-400 font-bold text-lg">50</span>
                <span>100 (Sangat banyak)</span>
            </div>
        </div>

        <!-- Kondisi Rumah -->
        <div>
            <label class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Skor Kondisi Tempat Tinggal</span>
                <span class="text-xs text-gray-500">0 - 100</span>
            </label>
            <input type="range" name="house_condition_score" min="0" max="100" value="{{ old('house_condition_score', 50) }}" 
                class="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-purple-500"
                oninput="document.getElementById('house-value').textContent = this.value" required>
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>0 (Sangat buruk)</span>
                <span id="house-value" class="text-purple-400 font-bold text-lg">50</span>
                <span>100 (Sangat baik)</span>
            </div>
            <p class="text-xs text-gray-500 mt-2">Penilaian berdasarkan foto rumah yang diunggah mahasiswa.</p>
        </div>

        <!-- Fuzzy Preview -->
        <div class="bg-gradient-to-r from-blue-600/10 to-purple-600/10 border border-blue-500/20 rounded-xl p-4">
            <h4 class="text-sm font-semibold mb-2 flex items-center gap-2">
                <i class="fas fa-brain text-blue-400"></i> Fuzzy Mamdani
            </h4>
            <p class="text-xs text-gray-400">
                Sistem akan otomatis melakukan: <strong class="text-white">Fuzzifikasi</strong> → <strong class="text-white">Inferensi Aturan</strong> → <strong class="text-white">Agregasi</strong> → <strong class="text-white">Defuzzifikasi</strong> untuk menghasilkan skor kelayakan 0-100.
            </p>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('staff.assessment') }}" class="px-6 py-2.5 border border-slate-700 rounded-lg text-gray-400 hover:text-white transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                <i class="fas fa-calculator"></i> Proses Assessment
            </button>
        </div>
    </form>
</div>
@endsection
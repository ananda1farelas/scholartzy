@extends('layouts.dashboard')

@section('title', 'Ajukan Beasiswa')

@section('content')
<div class="max-w-3xl mx-auto">
    @if($existingApplication)
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 text-center">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Pengajuan Sedang Aktif</h3>
            <p class="text-gray-400 mb-4">Anda memiliki pengajuan beasiswa yang sedang diproses.</p>
            <a href="{{ route('student.status') }}" class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                Lihat Status Pengajuan
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('student.apply.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Info Mahasiswa -->
            <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-300">Informasi Semester</p>
                        <p class="text-xs text-gray-400">
                            Anda sekarang semester <strong class="text-white">{{ $currentSemester }}</strong>. 
                            Silakan isi IPK untuk <strong class="text-white">{{ $requiredSemesters }} semester</strong> yang telah dilewati.
                        </p>
                    </div>
                </div>
            </div>

            <!-- IPK Per Semester -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-blue-400"></i> Nilai IPK per Semester
                </h3>
                <p class="text-gray-500 text-sm mb-4">Masukkan IPK untuk setiap semester yang telah selesai (0.00 - 4.00)</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @for($i = 1; $i <= $requiredSemesters; $i++)
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">
                            <i class="fas fa-book mr-1 text-blue-400"></i>Semester {{ $i }}
                        </label>
                        <input type="number" 
                               name="gpa_semester_{{ $i }}" 
                               step="0.01" 
                               min="0" 
                               max="4" 
                               value="{{ old('gpa_semester_' . $i, $existingGpas[$i] ?? '') }}"
                               class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-center text-lg font-bold"
                               placeholder="0.00"
                               required>
                        @error("gpa_semester_$i")
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endfor
                </div>

                <!-- Preview IPK Kumulatif -->
                <div class="mt-4 p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-400">IPK Kumulatif (Rata-rata)</span>
                        <span id="ipk-preview" class="text-2xl font-bold text-blue-400">-</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Otomatis dihitung dari rata-rata IPK per semester</p>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold mb-1">Dokumen Wajib</h3>
                <p class="text-gray-500 text-sm mb-4">Lengkapi dan unggah semua dokumen yang diperlukan</p>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Transkrip Nilai <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[transcript]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Kartu Keluarga <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[family_card]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Bukti Penghasilan <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[income_proof]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-image mr-1 text-green-400"></i>Foto Rumah <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[house_photo]" accept=".jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 5MB)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-2">
                        <i class="fas fa-trophy mr-1 text-yellow-400"></i>Sertifikat Prestasi (Opsional)
                    </label>
                    <input type="file" name="documents[achievement_certificate]" accept=".pdf,.jpg,.jpeg,.png" 
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                </div>
            </div>

            <!-- Catatan -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <label class="block text-gray-400 text-sm mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="3" 
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" 
                    placeholder="Tulis catatan jika ada...">{{ old('notes') }}</textarea>
            </div>

            <!-- Checkbox -->
            <div class="flex items-start gap-3 bg-slate-900/50 p-4 rounded-xl">
                <input type="checkbox" id="agree" class="mt-1 w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-600 focus:ring-blue-500" required>
                <label for="agree" class="text-sm text-gray-400">
                    Saya menyatakan bahwa semua data dan dokumen yang saya unggah adalah <strong class="text-white">benar dan valid</strong>. 
                    Saya bersedia menerima sanksi jika terbukti melakukan kecurangan.
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('student.dashboard') }}" class="px-6 py-2.5 border border-slate-700 rounded-lg text-gray-400 hover:text-white hover:border-slate-600 transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Pengajuan
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
// Auto-calculate IPK Kumulatif
document.addEventListener('DOMContentLoaded', function() {
    const gpaInputs = document.querySelectorAll('input[name^="gpa_semester_"]');
    const ipkPreview = document.getElementById('ipk-preview');
    
    function calculateGpa() {
        let total = 0;
        let count = 0;
        
        gpaInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val >= 0 && val <= 4) {
                total += val;
                count++;
            }
        });
        
        if (count > 0) {
            const average = (total / count).toFixed(2);
            ipkPreview.textContent = average;
            
            // Color coding
            if (average >= 3.5) {
                ipkPreview.className = 'text-2xl font-bold text-green-400';
            } else if (average >= 3.0) {
                ipkPreview.className = 'text-2xl font-bold text-blue-400';
            } else if (average >= 2.5) {
                ipkPreview.className = 'text-2xl font-bold text-yellow-400';
            } else {
                ipkPreview.className = 'text-2xl font-bold text-red-400';
            }
        } else {
            ipkPreview.textContent = '-';
            ipkPreview.className = 'text-2xl font-bold text-gray-500';
        }
    }
    
    gpaInputs.forEach(input => {
        input.addEventListener('input', calculateGpa);
    });
    
    // Calculate on load if values exist
    calculateGpa();
});
</script>
@endpush
@endsection
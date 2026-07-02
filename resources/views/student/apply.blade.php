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
        
        <!-- Peringatan Error dari Server (Back-End) -->
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-5 mb-6">
                <div class="flex items-center gap-2 text-red-400 font-bold mb-3">
                    <i class="fas fa-exclamation-triangle"></i> Oops! Ada data yang kurang sesuai:
                </div>
                <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Peringatan Error Ukuran File dari UI (Front-End) - Awalnya Disembunyikan -->
        <div id="file-size-error" class="hidden bg-red-500/10 border border-red-500/30 rounded-xl p-5 mb-6 shadow-lg shadow-red-900/20">
            <div class="flex items-center gap-2 text-red-400 font-bold mb-2">
                <i class="fas fa-times-circle text-xl"></i> Total File Terlalu Besar!
            </div>
            <p id="file-size-error-msg" class="text-sm text-red-300 leading-relaxed"></p>
        </div>

        <!-- Tambahkan ID 'scholarship-form' agar bisa ditangkap oleh JavaScript -->
        <form id="scholarship-form" method="POST" action="{{ route('student.apply.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Info Mahasiswa -->
            <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-5">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <i class="fas fa-hand-sparkles text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-md font-bold text-blue-300 mb-1">Halo, Calon Penerima Beasiswa!</h4>
                        <p class="text-sm text-gray-300">
                            Saat ini kamu berada di <strong class="text-white">Semester {{ $currentSemester }}</strong>. 
                            Mohon isikan nilai IPK dari <strong class="text-white">{{ $requiredSemesters }} semester</strong> yang telah kamu selesaikan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: IPK -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2 text-white">
                    <i class="fas fa-graduation-cap text-blue-400"></i> Nilai IPK per Semester
                </h3>
                <p class="text-gray-400 text-sm mb-6">Gunakan tanda titik (.) untuk desimal. Contoh: 3.85</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @for($i = 1; $i <= $requiredSemesters; $i++)
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-book mr-1 text-blue-400"></i>Semester {{ $i }}
                        </label>
                        <input type="number" name="gpa_semester_{{ $i }}" step="0.01" min="0" max="4" value="{{ old('gpa_semester_' . $i, $existingGpas[$i] ?? '') }}" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-center text-lg font-bold" placeholder="0.00" required>
                    </div>
                    @endfor
                </div>

                <div class="mt-6 p-5 bg-slate-800/80 rounded-xl border border-slate-700 flex justify-between items-center">
                    <span class="block text-sm font-medium text-gray-400">Estimasi IPK Kumulatif</span>
                    <span id="ipk-preview" class="text-3xl font-black text-blue-400">-</span>
                </div>
                
                <div class="mt-6 pt-6 border-t border-slate-800">
                    <label class="block text-gray-300 font-medium text-sm mb-2">
                        <i class="fas fa-file-pdf mr-1 text-red-400"></i>Upload Transkrip Nilai Asli <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="documents[transcript]" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" required>
                </div>
            </div>

            <!-- SECTION 2: TANGGUNGAN KELUARGA -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2 text-white">
                    <i class="fas fa-users text-purple-400"></i> Data Tanggungan Keluarga
                </h3>
                <p class="text-gray-400 text-sm mb-6">Data ini diambil secara otomatis dari halaman Profil Anda.</p>
                
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700 flex flex-col justify-center">
                        <p class="text-xs text-gray-500 mb-1">Jumlah Tanggungan (Tercatat)</p>
                        <p class="text-3xl font-black text-white">
                            {{ $student->parentGuardian?->dependents_count ?? 0 }} <span class="text-sm font-normal text-gray-400">Orang</span>
                        </p>
                        <p class="text-xs text-blue-400 mt-2"><i class="fas fa-info-circle"></i> Ubah di menu profil jika tidak sesuai.</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Upload Kartu Keluarga <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[family_card]" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer" required>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: BUKTI PENGHASILAN -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2 text-white">
                    <i class="fas fa-money-bill-wave text-green-400"></i> Data Penghasilan
                </h3>
                <p class="text-gray-400 text-sm mb-6">Data ini diambil secara otomatis dari halaman Profil Anda.</p>
                
                @php
                    $fatherIncome = $student->parentGuardian?->father_income ?? 0;
                    $motherIncome = $student->parentGuardian?->mother_income ?? 0;
                    $totalIncome = $fatherIncome + $motherIncome;
                @endphp

                <div class="grid md:grid-cols-3 gap-4 p-4 bg-slate-800/50 rounded-xl border border-slate-700">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Penghasilan Ayah</p>
                        <p class="text-lg font-bold text-white">Rp{{ number_format($fatherIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Penghasilan Ibu</p>
                        <p class="text-lg font-bold text-white">Rp{{ number_format($motherIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Total Pendapatan</p>
                        <p class="text-lg font-bold text-green-400">Rp{{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-800">
                    <label class="block text-gray-300 font-medium text-sm mb-2">
                        <i class="fas fa-file-pdf mr-1 text-red-400"></i>Upload Slip Gaji / Surat Keterangan Penghasilan <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="documents[income_proof]" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer" required>
                </div>
            </div>

            <!-- SECTION 4: FOTO RUMAH -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2 text-white">
                    <i class="fas fa-home text-orange-400"></i> Foto Kondisi Rumah
                </h3>
                <p class="text-gray-400 text-sm mb-6">Unggah 4 foto wajib dan 1 foto opsional.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php $photoLabels = ['Tampak Depan (Wajib)', 'Ruang Tamu (Wajib)', 'Ruang Keluarga (Wajib)', 'Dapur (Wajib)', 'Kamar Tidur (Opsional)']; @endphp
                    @foreach($photoLabels as $index => $label)
                    <div>
                        <label class="block text-gray-300 text-xs font-bold mb-2">{{ $label }}</label>
                        <input type="file" name="house_photos[]" accept=".jpg,.jpeg,.png" class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer border border-slate-700 rounded-lg bg-slate-800" {{ $index < 4 ? 'required' : '' }}>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- SECTION 5: SERTIFIKAT PRESTASI -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-bold mb-1 flex items-center gap-2 text-white">
                    <i class="fas fa-trophy text-yellow-400"></i> Sertifikat Prestasi (Opsional)
                </h3>
                <p class="text-gray-400 text-sm mb-4">Masukkan jumlah sertifikat, lalu kolom unggahan akan muncul secara otomatis.</p>
                
                <div class="mb-6">
                    <label class="block text-gray-300 font-medium text-sm mb-2">
                        <i class="fas fa-hashtag mr-1 text-yellow-400"></i>Jumlah Sertifikat
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" id="cert-count" min="0" max="10" value="0" class="w-24 px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-center text-lg font-bold">
                        <span class="text-gray-500 text-sm">Maksimal 10 Sertifikat</span>
                    </div>
                </div>

                <div id="cert-container" class="space-y-3">
                    <!-- Javascript akan mengisi bagian ini -->
                </div>
            </div>

            <!-- Catatan & Persetujuan -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <label class="block text-gray-400 text-sm mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white"></textarea>
            </div>

            <div class="bg-blue-900/20 border border-blue-500/30 p-5 rounded-xl">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="agree" class="mt-1 w-5 h-5 rounded border-slate-600 bg-slate-800 text-blue-600 cursor-pointer" required>
                    <label for="agree" class="text-sm text-gray-300 leading-relaxed cursor-pointer">
                        Saya menyatakan bahwa seluruh data yang diunggah adalah <strong>benar dan valid</strong>.
                    </label>
                </div>
            </div>

            <!-- UI Limit File Size -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 bg-slate-900 p-4 border border-slate-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500/20 rounded-lg">
                        <i class="fas fa-cloud-upload-alt text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-200">Kapasitas Upload</p>
                        <p class="text-xs text-gray-400">Maksimal total keseluruhan file: <strong class="text-white">50 MB</strong></p>
                    </div>
                </div>
                <button type="submit" id="submitBtn" disabled class="w-full sm:w-auto bg-gray-600 cursor-not-allowed text-white px-8 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                </button>
            </div>

            <script>
                const agreeCheckbox = document.getElementById('agree');
                const submitBtn = document.getElementById('submitBtn');

                agreeCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('bg-gray-600', 'cursor-not-allowed');
                        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                    } else {
                        submitBtn.disabled = true;
                        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                        submitBtn.classList.add('bg-gray-600', 'cursor-not-allowed');
                    }
                });
            </script>
        </form>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. SCRIPT UNTUK IPK OTOMATIS ---
    const gpaInputs = document.querySelectorAll('input[name^="gpa_semester_"]');
    const ipkPreview = document.getElementById('ipk-preview');
    
    function calculateGpa() {
        let total = 0, count = 0;
        gpaInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val >= 0 && val <= 4) { total += val; count++; }
        });
        
        if (count > 0) {
            const average = (total / count).toFixed(2);
            ipkPreview.textContent = average;
            if (average >= 3.5) ipkPreview.className = 'text-3xl font-black text-green-400';
            else if (average >= 3.0) ipkPreview.className = 'text-3xl font-black text-blue-400';
            else ipkPreview.className = 'text-3xl font-black text-yellow-400';
        } else {
            ipkPreview.textContent = '-';
            ipkPreview.className = 'text-3xl font-black text-gray-500';
        }
    }
    gpaInputs.forEach(input => input.addEventListener('input', calculateGpa));
    calculateGpa();

    // --- 2. SCRIPT UNTUK SERTIFIKAT DINAMIS ---
    const certCountInput = document.getElementById('cert-count');
    const certContainer = document.getElementById('cert-container');

    certCountInput.addEventListener('input', function() {
        let count = parseInt(this.value) || 0;
        
        if (count > 10) { count = 10; this.value = 10; } 
        else if (count < 0) { count = 0; this.value = 0; }

        certContainer.innerHTML = ''; 

        for (let i = 1; i <= count; i++) {
            const certBox = document.createElement('div');
            certBox.className = 'p-4 bg-slate-800/50 border border-slate-700 rounded-xl flex items-center justify-between gap-4';
            certBox.innerHTML = `
                <div class="flex-1">
                    <label class="block text-gray-300 text-sm font-bold mb-2">Upload Sertifikat #${i} <span class="text-red-400">*</span></label>
                    <input type="file" name="cert_files[]" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-yellow-600 file:text-white hover:file:bg-yellow-700 cursor-pointer border border-slate-700 rounded-lg bg-slate-800">
                </div>
                <i class="fas fa-certificate text-yellow-500/20 text-4xl hidden sm:block"></i>
            `;
            certContainer.appendChild(certBox);
        }
    });

    // --- 3. SCRIPT UNTUK VALIDASI TOTAL UKURAN FILE (Mencegah Error 413) ---
    const form = document.getElementById('scholarship-form');
    const errorDiv = document.getElementById('file-size-error');
    const errorMsg = document.getElementById('file-size-error-msg');
    const MAX_MB = 50; 
    const MAX_BYTES = MAX_MB * 1024 * 1024; // Konversi MB ke Bytes

    form.addEventListener('submit', function(e) {
        let totalSize = 0;
        
        // Ambil SEMUA input file di form saat tombol submit ditekan
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            if (input.files.length > 0) {
                for (let i = 0; i < input.files.length; i++) {
                    totalSize += input.files[i].size;
                }
            }
        });

        // Jika kebesaran, jegal form-nya agar tidak terkirim ke server!
        if (totalSize > MAX_BYTES) {
            e.preventDefault(); // Menghentikan proses submit
            
            const totalMB = (totalSize / (1024 * 1024)).toFixed(2);
            
            // Munculkan kotak error merah
            errorMsg.innerHTML = `Total ukuran file yang kamu pilih adalah <strong>${totalMB} MB</strong>, melebihi batas maksimal <strong>${MAX_MB} MB</strong>. Silakan kompres beberapa foto atau file PDF kamu agar ukurannya lebih kecil.`;
            errorDiv.classList.remove('hidden');
            
            // Auto-scroll ke atas agar mahasiswa langsung melihat errornya
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
</script>
@endpush
@endsection
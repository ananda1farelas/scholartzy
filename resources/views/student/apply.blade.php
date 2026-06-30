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
                    <div class="mt-4">
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Transkrip Nilai <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[transcript]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>
            </div>
            <!-- SECTION 2: KARTU KELUARGA & TANGGUNGAN -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <i class="fas fa-users text-purple-400"></i> Data Keluarga
                </h3>
                <p class="text-gray-500 text-sm mb-4">isi jumlah tanggungan dan upload Kartu Keluarga</p>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Input Jumlah Tanggungan -->
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-child mr-1 text-purple-400"></i>Jumlah Tanggungan Keluarga <span class="text-red-400">*</span>
                        </label>
                        <input type="number" 
                            name="dependents_count" 
                            min="0" 
                            max="20" 
                            value="{{ old('dependents_count', $student->parentGuardian?->dependents_count ?? '') }}"
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-center text-lg font-bold"
                            placeholder="Contoh: 3"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Total anggota keluarga yang ditanggung (termasuk orang tua)</p>
                        @error('dependents_count')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Kartu Keluarga <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[family_card]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: BUKTI PENGHASILAN -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-400"></i> Bukti Penghasilan
                </h3>
                <p class="text-gray-500 text-sm mb-4">Upload bukti penghasilan dan verifikasi data</p>

                @php
                    $parent = $student->parentGuardian ?? null;
                    
                    // Cek apakah data orang tua sudah diisi (berdasarkan NAMA, bukan penghasilan)
                    $hasFather = !empty($parent?->father_name);
                    $hasMother = !empty($parent?->mother_name);
                    
                    $fatherIncome = $parent?->father_income ?? 0;
                    $motherIncome = $parent?->mother_income ?? 0;
                    $totalIncome = $fatherIncome + $motherIncome;
                @endphp

                <!-- Data Penghasilan dari Profil -->
                @if($hasFather || $hasMother)
                    <div class="grid md:grid-cols-3 gap-4 mb-4 p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                        <!-- Ayah -->
                        @if($hasFather)
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Penghasilan Ayah</p>
                            @if($fatherIncome > 0)
                                <p class="text-lg font-bold text-white">
                                    Rp{{ number_format($fatherIncome, 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-lg font-bold text-gray-500">Tidak Berpenghasilan</p>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Ibu -->
                        @if($hasMother)
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Penghasilan Ibu</p>
                            @if($motherIncome > 0)
                                <p class="text-lg font-bold text-white">
                                    Rp{{ number_format($motherIncome, 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-lg font-bold text-gray-500">Tidak Berpenghasilan</p>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Total -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Total Penghasilan</p>
                            <p class="text-lg font-bold text-green-400">
                                Rp{{ number_format($totalIncome, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- WARNING: Data belum diisi sama sekali -->
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-6 text-center mb-4">
                        <i class="fas fa-exclamation-circle text-red-400 text-3xl mb-3"></i>
                        <h4 class="text-lg font-bold text-red-300 mb-2">Data Orang Tua Belum Lengkap</h4>
                        <p class="text-gray-400 text-sm mb-4">
                            Silakan lengkapi data orang tua di halaman profil terlebih dahulu.
                        </p>
                        <a href="{{ route('student.profile') }}" 
                        class="inline-block bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                            <i class="fas fa-user-edit mr-2"></i>Lengkapi Profil
                        </a>
                    </div>
                @endif

                <!-- Upload Bukti Penghasilan -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            <i class="fas fa-file-pdf mr-1 text-red-400"></i>Upload Bukti Penghasilan <span class="text-red-400">*</span>
                        </label>
                        <input type="file" name="documents[income_proof]" accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" required>
                        <p class="text-xs text-gray-500 mt-1">Slip gaji/Surat keterangan penghasilan (PDF, JPG, PNG, Max 5MB)</p>
                    </div>

                    <!-- Konfirmasi Data -->
                    <div class="flex items-center">
                        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4 w-full">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-yellow-400 mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-yellow-300 font-medium">Data diambil dari Profil Orang Tua</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Jika data penghasilan belum sesuai, silakan 
                                        <a href="{{ route('student.profile') }}" class="text-blue-400 hover:underline">update profil</a> 
                                        terlebih dahulu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: FOTO RUMAH (5 FOTO BERTAHAP) -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6" x-data="housePhotos()">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <i class="fas fa-home text-orange-400"></i> Foto Kondisi Rumah
                </h3>
                <p class="text-gray-500 text-sm mb-4">Upload 5 foto rumah secara bertahap</p>

                <!-- Progress Indicator -->
                <div class="flex items-center gap-2 mb-6">
                    <template x-for="(label, index) in photoLabels" :key="index">
                        <div class="flex-1 flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full transition-all duration-300"
                                :class="index < currentIndex ? 'bg-green-500' : (index === currentIndex ? 'bg-blue-500' : 'bg-slate-700')">
                            </div>
                            <span class="text-xs whitespace-nowrap" 
                                :class="index < currentIndex ? 'text-green-400' : (index === currentIndex ? 'text-blue-400' : 'text-gray-600')"
                                x-text="label">
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Current Photo Upload -->
                <div class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center transition-all hover:border-blue-500/50"
                    :class="{'border-green-500/50 bg-green-500/5': photos[currentIndex] !== null}">
                    
                    <template x-if="photos[currentIndex] === null">
                        <div>
                            <div class="w-16 h-16 mx-auto mb-4 bg-slate-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-camera text-2xl text-gray-500"></i>
                            </div>
                            <h4 class="text-lg font-medium mb-2" x-text="currentLabel"></h4>
                            <p class="text-gray-500 text-sm mb-4">Upload foto <span x-text="currentLabel.toLowerCase()"></span></p>
                            
                            <input type="file" 
                                :name="'house_photos[' + currentIndex + ']'"
                                accept=".jpg,.jpeg,.png"
                                @change="handleFileChange($event)"
                                class="hidden" 
                                :id="'house-photo-' + currentIndex"
                                required>
                            
                            <label :for="'house-photo-' + currentIndex" 
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition cursor-pointer">
                                <i class="fas fa-upload mr-2"></i>Pilih Foto
                            </label>
                        </div>
                    </template>

                    <template x-if="photos[currentIndex] !== null">
                        <div>
                            <div class="relative w-48 h-48 mx-auto mb-4 rounded-xl overflow-hidden border border-slate-700">
                                <img :src="photoPreviews[currentIndex]" class="w-full h-full object-cover" alt="Preview">
                                <div class="absolute top-2 right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            </div>
                            <p class="text-green-400 font-medium mb-2">
                                <i class="fas fa-check-circle mr-1"></i><span x-text="currentLabel"></span> berhasil diupload
                            </p>
                            <button type="button" @click="removePhoto(currentIndex)" 
                                    class="text-red-400 hover:text-red-300 text-sm underline">
                                Ganti Foto
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Photo Thumbnails -->
                <div class="grid grid-cols-5 gap-3 mt-6">
                    <template x-for="(label, index) in photoLabels" :key="index">
                        <div class="text-center p-3 rounded-lg border transition-all"
                            :class="{
                                'border-green-500 bg-green-500/10': photos[index] !== null,
                                'border-blue-500 bg-blue-500/10': index === currentIndex && photos[index] === null,
                                'border-slate-700 bg-slate-800/50': index > currentIndex && photos[index] === null
                            }">
                            <div class="w-10 h-10 mx-auto mb-2 rounded-full flex items-center justify-center text-sm font-bold"
                                :class="{
                                    'bg-green-500 text-white': photos[index] !== null,
                                    'bg-blue-500 text-white': index === currentIndex && photos[index] === null,
                                    'bg-slate-700 text-gray-500': index > currentIndex && photos[index] === null
                                }">
                                <template x-if="photos[index] !== null">
                                    <i class="fas fa-check"></i>
                                </template>
                                <template x-if="photos[index] === null">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <p class="text-xs" x-text="label"></p>
                        </div>
                    </template>
                </div>

                <!-- Hidden inputs for form submission -->
                <template x-for="(photo, index) in photos" :key="index">
                    <input type="hidden" :name="'house_photo_' + index" :value="photo ? photo.name : ''">
                </template>
            </div>

            @push('scripts')
            <script>
            function housePhotos() {
                return {
                    currentIndex: 0,
                    photoLabels: ['Tampak Depan', 'Ruang Tamu', 'Ruang Keluarga', 'Dapur', 'Kamar Tidur'],
                    photos: [null, null, null, null, null],
                    photoPreviews: ['', '', '', '', ''],
                    
                    get currentLabel() {
                        return this.photoLabels[this.currentIndex];
                    },
                    
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        
                        this.photos[this.currentIndex] = file;
                        this.photoPreviews[this.currentIndex] = URL.createObjectURL(file);
                        
                        // Move to next photo if available
                        if (this.currentIndex < 4) {
                            setTimeout(() => {
                                this.currentIndex++;
                            }, 500);
                        }
                    },
                    
                    removePhoto(index) {
                        this.photos[index] = null;
                        this.photoPreviews[index] = '';
                        this.currentIndex = index;
                    }
                }
            }
            </script>
            @endpush

            <!-- SECTION 5: SERTIFIKAT PRESTASI -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6" x-data="certificates()">
                <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                    <i class="fas fa-trophy text-yellow-400"></i> Sertifikat Prestasi
                </h3>
                <p class="text-gray-500 text-sm mb-4">Masukkan jumlah sertifikat dan upload bukti</p>

                <!-- Input Jumlah Sertifikat -->
                <div class="mb-6">
                    <label class="block text-gray-400 text-sm mb-2">
                        <i class="fas fa-hashtag mr-1 text-yellow-400"></i>Jumlah Sertifikat yang Dimiliki
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="number" 
                            name="certificate_count" 
                            min="0" 
                            max="50"
                            x-model="certCount"
                            @change="updateCertInputs()"
                            class="w-32 px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-center text-lg font-bold"
                            placeholder="0">
                        <span class="text-gray-500 text-sm">sertifikat</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Masukkan 0 jika tidak memiliki sertifikat prestasi</p>
                </div>

                <!-- Dynamic Upload Fields -->
                <div class="space-y-4">
                    <template x-for="(cert, index) in certificates" :key="index">
                        <div class="p-4 bg-slate-800/50 rounded-xl border border-slate-700/50 transition-all"
                            :class="{'border-yellow-500/30': cert.file !== null}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-yellow-400">
                                    <i class="fas fa-certificate mr-2"></i>Sertifikat #<span x-text="index + 1"></span>
                                </h4>
                                <span x-show="cert.file !== null" class="text-xs text-green-400">
                                    <i class="fas fa-check mr-1"></i>Ready
                                </span>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Nama Sertifikat -->
                                <div>
                                    <label class="block text-gray-500 text-xs mb-1">Nama/Judul Sertifikat</label>
                                    <input type="text" 
                                        :name="'cert_name_' + index"
                                        x-model="cert.name"
                                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-sm"
                                        placeholder="Contoh: Juara 1 Lomba Programming">
                                </div>

                                <!-- Upload File -->
                                <div>
                                    <label class="block text-gray-500 text-xs mb-1">Upload File</label>
                                    <input type="file" 
                                        :name="'cert_file_' + index"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        @change="handleCertFile($event, index)"
                                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-yellow-600 file:text-white">
                                </div>
                            </div>

                            <!-- Preview -->
                            <div x-show="cert.preview" class="mt-3">
                                <div class="flex items-center gap-3 p-2 bg-slate-900/50 rounded-lg">
                                    <img :src="cert.preview" class="w-16 h-16 object-cover rounded border border-slate-700" alt="Preview">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white truncate" x-text="cert.file?.name"></p>
                                        <p class="text-xs text-gray-500" x-text="cert.file ? (cert.file.size / 1024 / 1024).toFixed(2) + ' MB' : ''"></p>
                                    </div>
                                    <button type="button" @click="removeCert(index)" class="text-red-400 hover:text-red-300 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Add More Button -->
                <button type="button" 
                        @click="addCert()"
                        x-show="certificates.length < 50"
                        class="mt-4 w-full py-3 border-2 border-dashed border-slate-700 rounded-xl text-gray-500 hover:text-yellow-400 hover:border-yellow-500/50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Sertifikat Lainnya</span>
                </button>

                <p class="text-xs text-gray-500 mt-2 text-center">
                    <span x-text="certificates.length"></span> dari 50 sertifikat
                </p>
            </div>

            @push('scripts')
            <script>
            function certificates() {
                return {
                    certCount: 0,
                    certificates: [],
                    
                    updateCertInputs() {
                        const count = parseInt(this.certCount) || 0;
                        const current = this.certificates.length;
                        
                        if (count > current) {
                            // Add more
                            for (let i = current; i < count; i++) {
                                this.certificates.push({
                                    name: '',
                                    file: null,
                                    preview: ''
                                });
                            }
                        } else if (count < current) {
                            // Remove excess
                            this.certificates = this.certificates.slice(0, count);
                        }
                    },
                    
                    addCert() {
                        if (this.certificates.length < 50) {
                            this.certificates.push({
                                name: '',
                                file: null,
                                preview: ''
                            });
                            this.certCount = this.certificates.length;
                        }
                    },
                    
                    removeCert(index) {
                        this.certificates.splice(index, 1);
                        this.certCount = this.certificates.length;
                    },
                    
                    handleCertFile(event, index) {
                        const file = event.target.files[0];
                        if (!file) return;
                        
                        this.certificates[index].file = file;
                        this.certificates[index].preview = URL.createObjectURL(file);
                    }
                }
            }
            </script>
            @endpush

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
@extends('layouts.app')

@section('title', 'Scholartzy - Sistem Pendukung Keputusan Beasiswa')

@section('content')
<div x-data="{ 
    showLogin: false, 
    showRegister: false,
    showMobileMenu: false,
    activeTab: 'login'
}" class="min-h-screen bg-slate-900 text-white overflow-x-hidden">

    <!-- ========== NAVBAR ========== -->
    <nav class="fixed w-full z-50 transition-all duration-300" :class="showMobileMenu ? 'bg-slate-900/95 backdrop-blur-lg' : 'bg-transparent'" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">Scholartzy</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-gray-300 hover:text-white transition text-sm font-medium">Fitur</a>
                    <a href="#cara-kerja" class="text-gray-300 hover:text-white transition text-sm font-medium">Cara Kerja</a>
                    <a href="#tentang" class="text-gray-300 hover:text-white transition text-sm font-medium">Tentang</a>
                    <button @click="showLogin = true; activeTab = 'login'" class="text-gray-300 hover:text-white transition text-sm font-medium">Login</button>
                    <button @click="showRegister = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-semibold transition shadow-lg shadow-blue-600/25">
                        Daftar Sekarang
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="showMobileMenu = !showMobileMenu" class="md:hidden text-white text-xl">
                    <i class="fas" :class="showMobileMenu ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="showMobileMenu" x-transition class="md:hidden bg-slate-900/95 backdrop-blur-lg border-t border-gray-800">
            <div class="px-4 py-4 space-y-3">
                <a href="#fitur" @click="showMobileMenu = false" class="block text-gray-300 hover:text-white py-2">Fitur</a>
                <a href="#cara-kerja" @click="showMobileMenu = false" class="block text-gray-300 hover:text-white py-2">Cara Kerja</a>
                <a href="#tentang" @click="showMobileMenu = false" class="block text-gray-300 hover:text-white py-2">Tentang</a>
                <button @click="showLogin = true; showMobileMenu = false; activeTab = 'login'" class="w-full text-left text-gray-300 hover:text-white py-2">Login</button>
                <button @click="showRegister = true; showMobileMenu = false" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold">Daftar Sekarang</button>
            </div>
        </div>
    </nav>

    <!-- ========== HERO SECTION ========== -->
    <section class="relative min-h-screen flex items-center pt-20 lg:pt-0 hero-pattern px-4">
        <div class="max-w-7xl mx-auto relative z-10 py-8 lg:py-0">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 rounded-full px-4 py-1.5 mx-auto lg:mx-0">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                        <span class="text-blue-300 text-sm font-medium">Sistem Terintegrasi Kampus</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold leading-tight">
                        Seleksi Beasiswa<br>
                        <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 gradient-text">Lebih Cerdas</span>
                    </h1>

                    <p class="text-lg lg:text-xl text-gray-400 leading-relaxed max-w-lg mx-auto lg:mx-0">
                        Sistem Pendukung Keputusan berbasis <strong class="text-white">Logika Fuzzy Mamdani</strong> untuk menilai kelayakan penerima beasiswa secara objektif, transparan, dan terstruktur.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        <button @click="showRegister = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 lg:px-8 py-3 lg:py-4 rounded-xl font-semibold text-base lg:text-lg transition shadow-xl shadow-blue-600/25 flex items-center justify-center gap-2">
                            Mulai Pengajuan
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button @click="document.getElementById('cara-kerja').scrollIntoView({behavior:'smooth'})" class="glass hover:bg-white/10 text-white px-6 lg:px-8 py-3 lg:py-4 rounded-xl font-semibold text-base lg:text-lg transition flex items-center justify-center gap-2">
                            <i class="fas fa-play-circle"></i>
                            Pelajari
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="flex justify-center lg:justify-start gap-6 lg:gap-8 pt-2">
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-white">5</div>
                            <div class="text-gray-500 text-xs lg:text-sm">Kriteria</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-white">100%</div>
                            <div class="text-gray-500 text-xs lg:text-sm">Objektif</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-2xl lg:text-3xl font-bold text-white">3</div>
                            <div class="text-gray-500 text-xs lg:text-sm">Role</div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Hidden on mobile, show on lg -->
                <div class="relative hidden lg:block">
                    <div class="relative bg-slate-800 rounded-2xl border border-slate-700 shadow-2xl overflow-hidden">
                        <!-- Browser Chrome -->
                        <div class="bg-slate-900 px-4 py-3 flex items-center gap-2 border-b border-slate-700">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="flex-1 mx-4">
                                <div class="bg-slate-800 rounded-md px-3 py-1 text-xs text-gray-500 text-center">scholartzy.ac.id/student/dashboard</div>
                            </div>
                        </div>
                        <!-- Mock Dashboard -->
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <div class="h-4 bg-slate-700 rounded w-32"></div>
                                <div class="h-8 w-8 bg-blue-600 rounded-lg"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-slate-700/50 rounded-xl p-4 space-y-2">
                                    <div class="h-3 bg-slate-600 rounded w-16"></div>
                                    <div class="h-6 bg-blue-500/30 rounded w-12"></div>
                                </div>
                                <div class="bg-slate-700/50 rounded-xl p-4 space-y-2">
                                    <div class="h-3 bg-slate-600 rounded w-16"></div>
                                    <div class="h-6 bg-green-500/30 rounded w-12"></div>
                                </div>
                                <div class="bg-slate-700/50 rounded-xl p-4 space-y-2">
                                    <div class="h-3 bg-slate-600 rounded w-16"></div>
                                    <div class="h-6 bg-purple-500/30 rounded w-12"></div>
                                </div>
                            </div>
                            <div class="bg-slate-700/30 rounded-xl p-4 space-y-3">
                                <div class="flex justify-between">
                                    <div class="h-3 bg-slate-600 rounded w-24"></div>
                                    <div class="h-3 bg-green-500/50 rounded w-16"></div>
                                </div>
                                <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 w-3/4 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating Badge -->
                    <div class="absolute -bottom-4 -left-4 glass-dark rounded-xl px-4 py-3 flex items-center gap-3 animate-float">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold">Terverifikasi</div>
                            <div class="text-xs text-gray-400">Data lengkap</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== FITUR SECTION ========== -->
    <section id="fitur" class="py-24 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-blue-400 font-semibold text-sm uppercase tracking-wider mb-3">Fitur Unggulan</h2>
                <h3 class="text-4xl font-bold mb-4">Semua yang Anda Butuhkan</h3>
                <p class="text-gray-400 max-w-2xl mx-auto">Sistem lengkap untuk mengelola seluruh proses seleksi beasiswa dari pengajuan hingga penentuan penerima.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Fitur 1 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-blue-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-blue-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-500/20 transition">
                        <i class="fas fa-file-alt text-2xl text-blue-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Pengajuan Online</h4>
                    <p class="text-gray-400 leading-relaxed">Mahasiswa dapat mengajukan beasiswa kapan saja melalui formulir online dengan mengunggah dokumen pendukung.</p>
                </div>

                <!-- Fitur 2 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-purple-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-purple-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-purple-500/20 transition">
                        <i class="fas fa-check-double text-2xl text-purple-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Verifikasi Data</h4>
                    <p class="text-gray-400 leading-relaxed">Staff Kemahasiswaan memverifikasi kelengkapan data dan dokumen pengajuan secara menyeluruh.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-teal-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-teal-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-teal-500/20 transition">
                        <i class="fas fa-brain text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Fuzzy Mamdani</h4>
                    <p class="text-gray-400 leading-relaxed">Penilaian kelayakan menggunakan metode Fuzzy Mamdani dengan 5 kriteria untuk hasil yang objektif.</p>
                </div>

                <!-- Fitur 4 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-pink-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-pink-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-pink-500/20 transition">
                        <i class="fas fa-chart-line text-2xl text-pink-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Monitoring Real-time</h4>
                    <p class="text-gray-400 leading-relaxed">Admin dapat memantau seluruh data pengajuan, verifikasi, dan hasil penilaian melalui dashboard.</p>
                </div>

                <!-- Fitur 5 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-yellow-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-yellow-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-500/20 transition">
                        <i class="fas fa-bell text-2xl text-yellow-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Status Transparan</h4>
                    <p class="text-gray-400 leading-relaxed">Mahasiswa dapat melihat status pengajuan dan hasil seleksi secara real-time.</p>
                </div>

                <!-- Fitur 6 -->
                <div class="group bg-slate-900 border border-slate-800 rounded-2xl p-8 hover:border-green-500/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-green-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 transition">
                        <i class="fas fa-shield-alt text-2xl text-green-400"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Keamanan Data</h4>
                    <p class="text-gray-400 leading-relaxed">Sistem dilengkapi autentikasi berbasis peran dan enkripsi password untuk keamanan data pengguna.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CARA KERJA SECTION ========== -->
    <section id="cara-kerja" class="py-24 bg-slate-900 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-purple-400 font-semibold text-sm uppercase tracking-wider mb-3">Alur Proses</h2>
                <h3 class="text-4xl font-bold mb-4">Cara Kerja Sistem</h3>
                <p class="text-gray-400 max-w-2xl mx-auto">Proses seleksi beasiswa yang terstruktur dan transparan dari awal hingga akhir.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 text-center relative z-10">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold border-4 border-slate-900">1</div>
                        <h4 class="text-lg font-bold mb-2">Registrasi</h4>
                        <p class="text-gray-400 text-sm">Mahasiswa membuat akun dan melengkapi data profil serta orang tua.</p>
                    </div>
                    <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-blue-600/50"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 text-center relative z-10">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold border-4 border-slate-900">2</div>
                        <h4 class="text-lg font-bold mb-2">Pengajuan</h4>
                        <p class="text-gray-400 text-sm">Mengisi formulir pengajuan dan mengunggah dokumen pendukung.</p>
                    </div>
                    <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-purple-600/50"></div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 text-center relative z-10">
                        <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold border-4 border-slate-900">3</div>
                        <h4 class="text-lg font-bold mb-2">Verifikasi</h4>
                        <p class="text-gray-400 text-sm">Staff memverifikasi kelengkapan data dan dokumen pengajuan.</p>
                    </div>
                    <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-teal-600/50"></div>
                </div>

                <!-- Step 4 -->
                <div class="relative">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 text-center relative z-10">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold border-4 border-slate-900">4</div>
                        <h4 class="text-lg font-bold mb-2">Penilaian</h4>
                        <p class="text-gray-400 text-sm">Sistem menghitung kelayakan menggunakan Fuzzy Mamdani dan menampilkan hasil.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== KRITERIA SECTION ========== -->
    <section class="py-24 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-teal-400 font-semibold text-sm uppercase tracking-wider mb-3">Kriteria Penilaian</h2>
                    <h3 class="text-4xl font-bold mb-6">5 Faktor Penentu Kelayakan</h3>
                    <p class="text-gray-400 mb-8 leading-relaxed">Metode Fuzzy Mamdani mengevaluasi setiap pengajuan berdasarkan lima kriteria utama yang telah ditentukan untuk memastikan objektivitas dalam seleksi.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-star text-blue-400"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">Indeks Prestasi Kumulatif (IPK)</h4>
                                <p class="text-gray-400 text-sm">Nilai akademik mahasiswa sebagai indikator prestasi belajar.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-wallet text-purple-400"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">Total Penghasilan Keluarga</h4>
                                <p class="text-gray-400 text-sm">Penghasilan gabungan orang tua/wali sebagai ukuran kemampuan ekonomi.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-teal-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-users text-teal-400"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">Jumlah Tanggungan Keluarga</h4>
                                <p class="text-gray-400 text-sm">Banyaknya anggota keluarga yang menjadi tanggungan.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-pink-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-trophy text-pink-400"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">Prestasi Mahasiswa</h4>
                                <p class="text-gray-400 text-sm">Pencapaian akademik dan non-akademik yang dimiliki.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-home text-yellow-400"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">Kondisi Tempat Tinggal</h4>
                                <p class="text-gray-400 text-sm">Penilaian kondisi rumah berdasarkan dokumen foto yang diunggah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-600/20 to-purple-600/20 rounded-3xl p-8 border border-slate-700">
                        <div class="text-center mb-6">
                            <div class="inline-block p-4 bg-slate-800 rounded-2xl mb-4">
                                <i class="fas fa-calculator text-4xl text-blue-400"></i>
                            </div>
                            <h4 class="text-2xl font-bold">Fuzzy Mamdani</h4>
                            <p class="text-gray-400 mt-2">Metode inferensi fuzzy yang menghasilkan nilai kelayakan kontinu untuk penentuan prioritas yang lebih adil.</p>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                                <span class="text-sm font-medium">Fuzzifikasi</span>
                                <span class="text-xs bg-blue-500/20 text-blue-300 px-2 py-1 rounded">Input → Himpunan Fuzzy</span>
                            </div>
                            <div class="flex justify-center"><i class="fas fa-arrow-down text-gray-600"></i></div>
                            <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                                <span class="text-sm font-medium">Inferensi Aturan</span>
                                <span class="text-xs bg-purple-500/20 text-purple-300 px-2 py-1 rounded">IF-THEN Rules</span>
                            </div>
                            <div class="flex justify-center"><i class="fas fa-arrow-down text-gray-600"></i></div>
                            <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                                <span class="text-sm font-medium">Agregasi</span>
                                <span class="text-xs bg-teal-500/20 text-teal-300 px-2 py-1 rounded">Gabungan Output</span>
                            </div>
                            <div class="flex justify-center"><i class="fas fa-arrow-down text-gray-600"></i></div>
                            <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                                <span class="text-sm font-medium">Defuzzifikasi</span>
                                <span class="text-xs bg-green-500/20 text-green-300 px-2 py-1 rounded">Nilai Crisp</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== TENTANG SECTION ========== -->
    <section id="tentang" class="py-24 bg-slate-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-pink-400 font-semibold text-sm uppercase tracking-wider mb-3">Tentang Sistem</h2>
            <h3 class="text-4xl font-bold mb-6">Scholartzy</h3>
            <p class="text-gray-400 text-lg leading-relaxed mb-8">
                Scholartzy adalah Sistem Pendukung Keputusan Kelayakan Penerima Beasiswa berbasis web yang dirancang untuk membantu proses seleksi beasiswa secara lebih <span class="text-white font-semibold">objektif</span> dan <span class="text-white font-semibold">terstruktur</span>. 
                Dengan memanfaatkan metode <span class="text-blue-400 font-semibold">Fuzzy Mamdani</span>, sistem ini mampu mengolah data kualitatif dan kuantitatif menjadi rekomendasi yang dapat dipertanggungjawabkan.
            </p>
            
            <div class="grid grid-cols-3 gap-8 mt-12">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-1">Laravel 11</div>
                    <div class="text-gray-500 text-sm">Framework Backend</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-1">MySQL</div>
                    <div class="text-gray-500 text-sm">Database</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-teal-400 mb-1">Fuzzy Logic</div>
                    <div class="text-gray-500 text-sm">Metode Penilaian</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CTA SECTION ========== -->
    <section class="py-24 bg-gradient-to-br from-blue-900/50 to-purple-900/50 relative overflow-hidden">
        <div class="absolute inset-0 hero-pattern opacity-30"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl font-bold mb-6">Siap Mengajukan Beasiswa?</h2>
            <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">Daftar sekarang dan mulai pengajuan beasiswa Anda dengan sistem yang transparan dan objektif.</p>
            <button @click="showRegister = true" class="bg-white text-slate-900 px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition shadow-2xl">
                Daftar Gratis Sekarang
            </button>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="bg-slate-950 border-t border-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <span class="text-xl font-bold">Scholartzy</span>
                </div>
                <div class="text-gray-500 text-sm text-center md:text-right">
                    <p> Sistem Pendukung Keputusan Kelayakan Penerima Beasiswa</p>
                    <p>Dibangun dengan Laravel 11 & MySQL</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========== LOGIN MODAL ========== -->
    <div x-show="showLogin" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showLogin" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showLogin = false"
             class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <!-- Modal Content -->
        <div x-show="showLogin"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="showLogin = false"
             class="relative bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Header -->
            <div class="px-8 pt-8 pb-4 text-center">
                <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold">Selamat Datang Kembali</h3>
                <p class="text-gray-400 text-sm mt-1">Masuk ke akun Scholartzy Anda</p>
                <button @click="showLogin = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-300 text-sm font-medium mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-500" 
                            placeholder="email@example.com" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-medium mb-2">Password</label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-500" 
                            placeholder="••••••••" required>
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-lg shadow-blue-600/25">
                        Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-400 text-sm">
                        Belum punya akun? 
                        <button @click="showLogin = false; showRegister = true" class="text-blue-400 font-semibold hover:underline">Daftar sekarang</button>
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-800 text-center text-xs text-gray-600">
                    <p>Akun Testing: admin@scholartzy.com | staff@scholartzy.com</p>
                    <p>Password: password</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== REGISTER MODAL ========== -->
    <div x-show="showRegister" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="showRegister" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showRegister = false"
             class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

        <!-- Modal Content -->
        <div x-show="showRegister"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="showRegister = false"
             class="relative bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden">
            
            <!-- Header -->
            <div class="px-8 pt-8 pb-4 text-center relative">
                <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-green-400 text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold">Buat Akun Mahasiswa</h3>
                <p class="text-gray-400 text-sm mt-1">Lengkapi data diri Anda untuk mulai mengajukan beasiswa</p>
                <button @click="showRegister = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8 max-h-[70vh] overflow-y-auto">
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Akun -->
                        <div class="col-span-2">
                            <h4 class="text-sm font-semibold text-gray-300 mb-3 border-b border-slate-700 pb-2">Informasi Akun</h4>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Password</label>
                            <input type="password" name="password" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <!-- Data Mahasiswa -->
                        <div class="col-span-2 mt-2">
                            <h4 class="text-sm font-semibold text-gray-300 mb-3 border-b border-slate-700 pb-2">Data Mahasiswa</h4>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">NIM</label>
                            <input type="text" name="student_number" value="{{ old('student_number') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Nama di KTM</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Jenis Kelamin</label>
                            <select name="gender" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                                <option value="">Pilih</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">No. Telepon</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Program Studi</label>
                            <input type="text" name="study_program" value="{{ old('study_program') }}" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Semester</label>
                            <input type="number" name="semester" value="{{ old('semester') }}" min="1" max="14"
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>
                        </div>

                        <div class="col-span-2 mb-1">
                            <label class="block text-gray-400 text-sm mb-1">Alamat Lengkap</label>
                            <textarea name="address" rows="2" 
                                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-white text-sm" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-lg shadow-green-600/25 mt-4">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <p class="text-gray-400 text-sm">
                        Sudah punya akun? 
                        <button @click="showRegister = false; showLogin = true" class="text-green-400 font-semibold hover:underline">Login di sini</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('bg-slate-900/95', 'backdrop-blur-lg', 'shadow-lg');
            navbar.classList.remove('bg-transparent');
        } else {
            navbar.classList.remove('bg-slate-900/95', 'backdrop-blur-lg', 'shadow-lg');
            navbar.classList.add('bg-transparent');
        }
    });
</script>
@endpush
@endsection
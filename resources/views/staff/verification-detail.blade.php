@extends('layouts.dashboard')

@section('title', 'Detail Verifikasi Pengajuan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" @if($application->application_status === 'pending') x-data="verificationApp()" @endif>
    
    <div class="flex items-center justify-between bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.verification') }}" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left text-xl"></i></a>
            <h2 class="text-xl font-bold">Verifikasi Dokumen #{{ $application->application_id }}</h2>
        </div>
        <span class="text-sm text-gray-500"><i class="far fa-clock mr-1"></i>{{ $application->application_date->format('d F Y') }}</span>
    </div>

    @if($application->application_status === 'pending')
    <form method="POST" action="{{ route('staff.verification.process', $application->application_id) }}" class="space-y-6">
        @csrf
        <input type="hidden" name="status" :value="finalStatus">
    @else
    <div class="space-y-6">
    @endif

        <!-- INFO MAHASISWA -->
        <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-5">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-500/20 rounded-lg"><i class="fas fa-user-graduate text-blue-400 text-xl"></i></div>
                <div class="w-full">
                    <h4 class="text-md font-bold text-blue-300 mb-2">{{ $application->student->full_name }} ({{ $application->student->student_number }})</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-300">
                        <div><p class="text-xs text-gray-500">Program Studi</p><p>{{ $application->student->study_program }}</p></div>
                        <div><p class="text-xs text-gray-500">Semester</p><p>Semester {{ $application->student->semester }}</p></div>
                        <div><p class="text-xs text-gray-500">No. HP</p><p>{{ $application->student->phone_number }}</p></div>
                        <div><p class="text-xs text-gray-500">Email</p><p>{{ $application->student->user->email }}</p></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. CARD IPK -->
        <div class="bg-slate-900 border rounded-xl p-6 shadow-lg transition-all" :class="getCardClass('ipk')">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-graduation-cap text-blue-400"></i> Validasi Transkrip IPK</h3>
            @php $transkrip = $application->documents->where('document_type', 'transcript')->first(); @endphp
            @if($transkrip)
                <a href="{{ asset('storage/' . $transkrip->file_path) }}" target="_blank" class="flex items-center gap-3 w-full px-4 py-3 bg-slate-800 border border-slate-700 hover:border-blue-500 rounded-lg text-white transition-all"><i class="fas fa-eye text-blue-400"></i> Cek Dokumen Transkrip Asli</a>
            @else
                <p class="text-red-400 text-sm">File Transkrip tidak ditemukan.</p>
            @endif

            @if($application->application_status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-800/50 flex gap-3">
                <button type="button" @click="setCheck('ipk', 'verified')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.ipk === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-check"></i> Dokumen Valid</button>
                <button type="button" @click="setCheck('ipk', 'rejected')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.ipk === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-times"></i> Dokumen Tidak Valid</button>
            </div>
            @endif
        </div>

        <!-- 2. CARD TANGGUNGAN -->
        <div class="bg-slate-900 border rounded-xl p-6 shadow-lg transition-all" :class="getCardClass('tanggungan')">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-users text-purple-400"></i> Validasi Kartu Keluarga</h3>
            <div class="mb-4 w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white font-bold">Tanggungan: {{ $application->student->parentGuardian->dependents_count ?? 0 }} Orang</div>
            @php $kk = $application->documents->where('document_type', 'family_card')->first(); @endphp
            @if($kk)
                <a href="{{ asset('storage/' . $kk->file_path) }}" target="_blank" class="flex items-center gap-3 w-full px-4 py-3 bg-slate-800 border border-slate-700 hover:border-purple-500 rounded-lg text-white transition-all font-bold"><i class="fas fa-external-link-alt text-purple-400"></i> Cek Kartu Keluarga</a>
            @else
                <p class="text-red-400 text-sm mt-3">File KK tidak ditemukan.</p>
            @endif

            @if($application->application_status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-800/50 flex gap-3">
                <button type="button" @click="setCheck('tanggungan', 'verified')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.tanggungan === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-check"></i> Dokumen Valid</button>
                <button type="button" @click="setCheck('tanggungan', 'rejected')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.tanggungan === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-times"></i> Dokumen Tidak Valid</button>
            </div>
            @endif
        </div>

        <!-- 3. CARD BUKTI PENGHASILAN -->
        <div class="bg-slate-900 border rounded-xl p-6 shadow-lg transition-all" :class="getCardClass('gaji')">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-money-bill-wave text-green-400"></i> Validasi Bukti Penghasilan</h3>
            @php 
                $pg = $application->student->parentGuardian;
                $totalIncome = ($pg->father_income ?? 0) + ($pg->mother_income ?? 0); 
            @endphp
            <div class="w-full mb-4 px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white font-bold">Total Gaji: Rp{{ number_format($totalIncome, 0, ',', '.') }}</div>
            @php $slipGaji = $application->documents->where('document_type', 'income_proof')->first(); @endphp
            @if($slipGaji)
                <a href="{{ asset('storage/' . $slipGaji->file_path) }}" target="_blank" class="flex items-center gap-3 w-full px-4 py-3 bg-slate-800 border border-slate-700 hover:border-green-500 rounded-lg text-white transition-all"><i class="fas fa-eye text-green-400"></i> Cek Slip Gaji Asli</a>
            @endif

            @if($application->application_status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-800/50 flex gap-3">
                <button type="button" @click="setCheck('gaji', 'verified')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.gaji === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-check"></i> Dokumen Valid</button>
                <button type="button" @click="setCheck('gaji', 'rejected')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.gaji === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-times"></i> Dokumen Tidak Valid</button>
            </div>
            @endif
        </div>

        <!-- 4. CARD RUMAH (VERIFIKASI & PENILAIAN) -->
        <div class="bg-slate-900 border rounded-xl p-6 shadow-lg transition-all" :class="getCardClass('rumah')">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-home text-orange-400"></i> Validasi & Penilaian Rumah</h3>
            @php $fotoRumah = $application->documents->where('document_type', 'house_photo'); @endphp
            @if($fotoRumah->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($fotoRumah as $foto)
                <a href="{{ asset('storage/' . $foto->file_path) }}" target="_blank" class="block relative border border-slate-700 rounded-xl overflow-hidden hover:border-orange-500 transition-all group">
                    <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-24 object-cover" alt="Foto Rumah">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center"><i class="fas fa-search-plus text-white text-xl"></i></div>
                </a>
                @endforeach
            </div>
            @else
            <div class="p-4 bg-slate-800 rounded-lg text-center text-gray-500">Tidak ada foto rumah yang dilampirkan.</div>
            @endif

            @if($application->application_status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-800/50">
                <p class="text-xs text-gray-400 mb-2">1. Apakah foto-foto di atas asli / bukan editan?</p>
                <div class="flex gap-3 mb-4">
                    <button type="button" @click="setCheck('rumah', 'verified')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.rumah === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-check"></i> Foto Asli (Valid)</button>
                    <button type="button" @click="setCheck('rumah', 'rejected')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.rumah === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-times"></i> Foto Palsu</button>
                </div>

                <!-- Bagian Penilaian Rumah muncul jika Staff mengeklik "Foto Asli" -->
                <div x-show="checks.rumah === 'verified'" x-transition class="p-4 bg-slate-800 rounded-lg border border-orange-500/50">
                    <p class="text-xs text-gray-400 mb-2">2. Penilaian Kelayakan Rumah</p>
                    <select name="house_condition_score" x-bind:required="checks.rumah === 'verified'" class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-orange-500 cursor-pointer">
                        <option value="" disabled selected>-- Amati foto di atas, lalu pilih kelayakannya --</option>
                        <option value="0">Layak Mendapat Beasiswa (Kondisi Rumah Terlihat Kurang Mampu)</option>
                        <option value="100">Tidak Layak (Kondisi Rumah Terlihat Bagus/Mewah)</option>
                    </select>
                </div>
            </div>
            @endif
        </div>

        <!-- 5. CARD PRESTASI -->
        <div class="bg-slate-900 border rounded-xl p-6 shadow-lg transition-all" :class="getCardClass('prestasi')">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-white"><i class="fas fa-trophy text-yellow-400"></i> Validasi Keaslian Sertifikat</h3>
            @php $sertifikat = $application->documents->where('document_type', 'achievement_certificate'); @endphp
            @if($sertifikat->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($sertifikat as $cert)
                <a href="{{ asset('storage/' . $cert->file_path) }}" target="_blank" class="px-3 py-2 bg-slate-800 border border-slate-700 hover:border-yellow-500 rounded-lg text-xs font-bold text-gray-300 hover:text-yellow-400 transition-all"><i class="fas fa-certificate"></i> File #{{ $loop->iteration }}</a>
                @endforeach
            </div>
            @else
            <div class="p-4 bg-slate-800 rounded-lg text-center text-gray-500 mb-4">Mahasiswa tidak melampirkan sertifikat.</div>
            @endif

            @if($application->application_status === 'pending')
            <div class="mt-4 pt-4 border-t border-slate-800/50 flex gap-3">
                <button type="button" @click="setCheck('prestasi', 'verified')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.prestasi === 'verified' ? 'bg-green-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-check"></i> Sertifikat Sah</button>
                <button type="button" @click="setCheck('prestasi', 'rejected')" class="flex-1 py-2 rounded-lg text-sm font-bold transition-all" :class="checks.prestasi === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-800 text-gray-400 hover:bg-slate-700'"><i class="fas fa-times"></i> Sertifikat Palsu</button>
            </div>
            @endif
        </div>

    @if($application->application_status === 'pending')
        <div class="bg-slate-900 border-2 rounded-xl p-6 shadow-2xl sticky bottom-6 z-20 transition-all duration-500" :class="finalStatus === 'rejected' ? 'border-red-500' : (finalStatus === 'verified' ? 'border-blue-500' : 'border-slate-700')">
            <label class="block text-sm font-medium text-gray-400 mb-2">Catatan Verifikasi <span x-show="finalStatus === 'rejected'" class="text-red-400">*Wajib diisi jika menolak</span></label>
            <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm" placeholder="Tulis catatan jika perlu..." :required="finalStatus === 'rejected'"></textarea>
            
            <div class="flex justify-end gap-3 mt-4">
                <button type="submit" :disabled="finalStatus === ''" class="px-8 py-3 rounded-lg font-bold transition-all flex items-center gap-2" :class="finalStatus === '' ? 'bg-slate-800 text-gray-500 cursor-not-allowed' : (finalStatus === 'verified' ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white')">
                    <i class="fas fa-save"></i> <span x-text="finalStatus === 'rejected' ? 'Kirim Penolakan Dokumen' : 'Simpan Data & Lanjut ke Assessment'"></span>
                </button>
            </div>
        </div>
    </form>
    @else
    </div>
    @endif
</div>

@push('scripts')
<script>
function verificationApp() {
    return {
        checks: { ipk: null, tanggungan: null, gaji: null, rumah: null, prestasi: null },
        get finalStatus() {
            const values = Object.values(this.checks);
            if (values.includes('rejected')) return 'rejected';
            if (values.includes(null)) return '';
            return 'verified';
        },
        setCheck(section, value) {
            this.checks[section] = value;
            if (value === 'rejected') window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        },
        getCardClass(section) {
            const status = this.checks[section];
            if (status === 'verified') return 'border-green-500/50 bg-green-900/10';
            if (status === 'rejected') return 'border-red-500/50 bg-red-900/10';
            return 'border-slate-800';
        }
    }
}
</script>
@endpush
@endsection
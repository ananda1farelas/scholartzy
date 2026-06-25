@extends('layouts.dashboard')

@section('title', 'Detail Verifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.verification') }}" class="text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-2xl font-bold">Detail Pengajuan #{{ $application->application_id }}</h2>
    </div>

    <!-- Status Banner -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-400">Status:</span>
            @php
                $statusConfig = [
                    'pending' => ['bg-yellow-500/10 text-yellow-400', 'Menunggu Verifikasi'],
                    'verified' => ['bg-blue-500/10 text-blue-400', 'Terverifikasi'],
                    'assessed' => ['bg-green-500/10 text-green-400', 'Assessment Selesai'],
                    'rejected' => ['bg-red-500/10 text-red-400', 'Ditolak'],
                ];
                $status = $statusConfig[$application->application_status] ?? ['bg-gray-500/10 text-gray-400', $application->application_status];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $status[0] }}">
                {{ $status[1] }}
            </span>
        </div>
        <span class="text-sm text-gray-500">{{ $application->application_date->format('d F Y H:i') }}</span>
    </div>

    <!-- Data Mahasiswa -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-user text-blue-400"></i> Data Mahasiswa
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                <p class="text-sm font-medium">{{ $application->student->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">NIM</p>
                <p class="text-sm font-medium">{{ $application->student->student_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Email</p>
                <p class="text-sm font-medium">{{ $application->student->user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Program Studi</p>
                <p class="text-sm font-medium">{{ $application->student->study_program }} - Semester {{ $application->student->semester }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
                <p class="text-sm font-medium">{{ $application->student->phone_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Jenis Kelamin</p>
                <p class="text-sm font-medium capitalize">{{ $application->student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 mb-1">Alamat</p>
                <p class="text-sm font-medium">{{ $application->student->address }}</p>
            </div>
        </div>
    </div>

    <!-- IPK Per Semester -->
    @php $semesterGpas = $application->student->semesterGpas()->orderBy('semester_number')->get(); @endphp
    @if($semesterGpas->isNotEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-graduation-cap text-blue-400"></i> IPK per Semester
        </h3>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
            @foreach($semesterGpas as $sg)
            <div class="bg-slate-800/50 rounded-xl p-3 text-center">
                <p class="text-[10px] text-gray-500 uppercase mb-1">Semester {{ $sg->semester_number }}</p>
                <p class="text-xl font-bold {{ $sg->gpa >= 3.5 ? 'text-green-400' : ($sg->gpa >= 3.0 ? 'text-blue-400' : ($sg->gpa >= 2.5 ? 'text-yellow-400' : 'text-red-400')) }}">
                    {{ number_format($sg->gpa, 2) }}
                </p>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between">
            <span class="text-sm text-gray-400">IPK Kumulatif (Rata-rata)</span>
            <span class="text-2xl font-bold text-white">{{ number_format($semesterGpas->avg('gpa'), 2) }}</span>
        </div>
    </div>
    @endif

    <!-- Data Orang Tua -->
    @if($application->student->parentGuardian)
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-users text-purple-400"></i> Data Keluarga
        </h3>
        @php $pg = $application->student->parentGuardian; @endphp
        
        <!-- Ayah -->
        @if($pg->father_name)
        <div class="mb-4 pb-4 border-b border-slate-800">
            <h4 class="text-sm font-semibold text-blue-400 mb-2">Ayah</h4>
            <div class="grid md:grid-cols-3 gap-3">
                <div><p class="text-xs text-gray-500">Nama</p><p class="text-sm">{{ $pg->father_name }}</p></div>
                <div><p class="text-xs text-gray-500">Pekerjaan</p><p class="text-sm">{{ $pg->father_occupation ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500">Penghasilan</p><p class="text-sm">Rp{{ number_format($pg->father_income ?? 0, 0, ',', '.') }}</p></div>
            </div>
        </div>
        @endif

        <!-- Ibu -->
        @if($pg->mother_name)
        <div class="mb-4 pb-4 border-b border-slate-800">
            <h4 class="text-sm font-semibold text-pink-400 mb-2">Ibu</h4>
            <div class="grid md:grid-cols-3 gap-3">
                <div><p class="text-xs text-gray-500">Nama</p><p class="text-sm">{{ $pg->mother_name }}</p></div>
                <div><p class="text-xs text-gray-500">Pekerjaan</p><p class="text-sm">{{ $pg->mother_occupation ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500">Penghasilan</p><p class="text-sm">Rp{{ number_format($pg->mother_income ?? 0, 0, ',', '.') }}</p></div>
            </div>
        </div>
        @endif

        <!-- Wali -->
        @if($pg->guardian_name)
        <div class="mb-4 pb-4 border-b border-slate-800">
            <h4 class="text-sm font-semibold text-yellow-400 mb-2">Wali</h4>
            <div class="grid md:grid-cols-3 gap-3">
                <div><p class="text-xs text-gray-500">Nama</p><p class="text-sm">{{ $pg->guardian_name }}</p></div>
                <div><p class="text-xs text-gray-500">Pekerjaan</p><p class="text-sm">{{ $pg->guardian_occupation ?? '-' }}</p></div>
                <div><p class="text-xs text-gray-500">Penghasilan</p><p class="text-sm">Rp{{ number_format($pg->guardian_income ?? 0, 0, ',', '.') }}</p></div>
            </div>
        </div>
        @endif

        <div class="bg-slate-800/50 rounded-lg p-3">
            <p class="text-xs text-gray-500">Total Penghasilan Keluarga</p>
            <p class="text-lg font-bold text-white">
                Rp{{ number_format(($pg->father_income ?? 0) + ($pg->mother_income ?? 0) + ($pg->guardian_income ?? 0), 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Jumlah Tanggungan: {{ $pg->dependents_count }} orang</p>
        </div>
    </div>
    @endif

    <!-- Dokumen -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-file-alt text-green-400"></i> Dokumen Unggahan
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($application->documents as $doc)
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
                   class="flex items-center gap-3 p-4 bg-slate-800/50 rounded-xl hover:bg-slate-800 transition border border-slate-700/50 hover:border-slate-600">
                    <i class="fas {{ $info[1] }} {{ $info[2] }} text-xl"></i>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ $info[0] }}</p>
                        <p class="text-xs text-gray-500">Lihat dokumen</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-600 ml-auto text-xs"></i>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Verifikasi Form (hanya untuk pending) -->
    @if($application->application_status === 'pending')
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Keputusan Verifikasi</h3>
        <form method="POST" action="{{ route('staff.verification.process', $application->application_id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-2">Status Verifikasi</label>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="verified" class="peer sr-only" required>
                        <div class="p-4 rounded-xl border border-slate-700 bg-slate-800/50 peer-checked:border-green-500 peer-checked:bg-green-500/10 text-center transition">
                            <i class="fas fa-check-circle text-2xl text-gray-500 peer-checked:text-green-400 mb-2"></i>
                            <p class="font-medium peer-checked:text-green-400">Verifikasi</p>
                            <p class="text-xs text-gray-500">Data lengkap & valid</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="rejected" class="peer sr-only">
                        <div class="p-4 rounded-xl border border-slate-700 bg-slate-800/50 peer-checked:border-red-500 peer-checked:bg-red-500/10 text-center transition">
                            <i class="fas fa-times-circle text-2xl text-gray-500 peer-checked:text-red-400 mb-2"></i>
                            <p class="font-medium peer-checked:text-red-400">Tolak</p>
                            <p class="text-xs text-gray-500">Data tidak valid</p>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" 
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white text-sm"
                    placeholder="Tambahkan catatan jika perlu..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('staff.verification') }}" class="px-6 py-2.5 border border-slate-700 rounded-lg text-gray-400 hover:text-white transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-gavel mr-2"></i>Ambil Keputusan
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
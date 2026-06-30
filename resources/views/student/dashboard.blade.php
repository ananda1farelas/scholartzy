@extends('layouts.dashboard')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/20 rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-400">
            @if($latestApplication)
                @switch($latestApplication->application_status)
                    @case('pending') Pengajuan beasiswa Anda sedang menunggu verifikasi. @break
                    @case('verified') Pengajuan Anda telah diverifikasi, menunggu proses assessment. @break
                    @case('assessed') Assessment telah selesai, silakan cek hasilnya. @break
                    @case('rejected') Pengajuan Anda ditolak. Silakan ajukan kembali periode berikutnya. @break
                @endswitch
            @else
                Anda belum mengajukan beasiswa. Silakan lengkapi profil dan ajukan sekarang!
            @endif
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
        <!-- Card 1: Total Pengajuan -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 lg:p-6">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-400 text-lg lg:text-xl"></i>
                </div>
                <span class="text-2xl lg:text-3xl font-bold">
                    {{ auth()->user()->student?->scholarshipApplications()->count() ?? 0 }}
                </span>
            </div>
            <div class="text-gray-500 text-xs lg:text-sm">Pengajuan Beasiswa</div>
        </div>

        <!-- Card 2: Sedang Diproses (FIXED) -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 lg:p-6">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-400 text-lg lg:text-xl"></i>
                </div>
                <span class="text-2xl lg:text-3xl font-bold">
                    {{ auth()->user()->student?->scholarshipApplications()->whereIn('application_status', ['pending', 'verified'])->count() ?? 0 }}
                </span>
            </div>
            <div class="text-gray-500 text-xs lg:text-sm">Sedang Diproses</div>
        </div>

        <!-- Card 3: Assessment Selesai -->
        <div class="col-span-2 lg:col-span-1 bg-slate-900 border border-slate-800 rounded-xl p-4 lg:p-6">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-400 text-lg lg:text-xl"></i>
                </div>
                <span class="text-2xl lg:text-3xl font-bold">
                    {{ auth()->user()->student?->scholarshipApplications()->where('application_status', 'assessed')->count() ?? 0 }}
                </span>
            </div>
            <div class="text-gray-500 text-xs lg:text-sm">Assessment Selesai</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('student.apply') }}" class="group bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-blue-500/50 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 transition">
                    <i class="fas fa-plus text-blue-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Ajukan Beasiswa</h3>
                    <p class="text-gray-500 text-sm">Isi formulir dan unggah dokumen</p>
                </div>
                <i class="fas fa-arrow-right text-gray-600 ml-auto group-hover:text-blue-400 transition"></i>
            </div>
        </a>

        <a href="{{ route('student.status') }}" class="group bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-purple-500/50 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-500/10 rounded-xl flex items-center justify-center group-hover:bg-purple-500/20 transition">
                    <i class="fas fa-search text-purple-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Cek Status</h3>
                    <p class="text-gray-500 text-sm">Lihat status pengajuan & hasil</p>
                </div>
                <i class="fas fa-arrow-right text-gray-600 ml-auto group-hover:text-purple-400 transition"></i>
            </div>
        </a>
    </div>

    <!-- Latest Application -->
    @if($latestApplication)
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Pengajuan Terakhir</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400">Tanggal Pengajuan</p>
                <p class="font-medium">{{ $latestApplication->application_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Status</p>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                        'verified' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'assessed' => 'bg-green-500/10 text-green-400 border-green-500/20',
                        'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$latestApplication->application_status] ?? 'bg-gray-500/10 text-gray-400' }}">
                    {{ ucfirst($latestApplication->application_status) }}
                </span>
            </div>
            <a href="{{ route('student.status') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
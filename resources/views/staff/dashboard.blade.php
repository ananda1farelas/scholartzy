@extends('layouts.dashboard')

@section('title', 'Dashboard Staff')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-yellow-600/10 to-orange-600/10 border border-yellow-500/20 rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-400">Pantau dan kelola proses seleksi beasiswa dari sini.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                </div>
                <span class="text-3xl font-bold">{{ $pendingCount }}</span>
            </div>
            <div class="text-gray-500 text-sm">Menunggu Verifikasi</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-blue-400 text-xl"></i>
                </div>
                <span class="text-3xl font-bold">{{ $verifiedCount }}</span>
            </div>
            <div class="text-gray-500 text-sm">Terverifikasi</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-400 text-xl"></i>
                </div>
                <span class="text-3xl font-bold">{{ $assessedCount }}</span>
            </div>
            <div class="text-gray-500 text-sm">Assessment Selesai</div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-400 text-xl"></i>
                </div>
                <span class="text-3xl font-bold">{{ $totalApplications }}</span>
            </div>
            <div class="text-gray-500 text-sm">Total Pengajuan</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('staff.verification') }}" class="group bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-yellow-500/50 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-yellow-500/10 rounded-xl flex items-center justify-center group-hover:bg-yellow-500/20 transition">
                    <i class="fas fa-check-double text-yellow-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Verifikasi</h3>
                    <p class="text-gray-500 text-sm">Cek & approve pengajuan</p>
                </div>
                <i class="fas fa-arrow-right text-gray-600 ml-auto group-hover:text-yellow-400 transition"></i>
            </div>
        </a>

        <a href="{{ route('staff.assessment') }}" class="group bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-blue-500/50 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 transition">
                    <i class="fas fa-calculator text-blue-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Assessment</h3>
                    <p class="text-gray-500 text-sm">Input nilai & Fuzzy</p>
                </div>
                <i class="fas fa-arrow-right text-gray-600 ml-auto group-hover:text-blue-400 transition"></i>
            </div>
        </a>

        <a href="{{ route('staff.results') }}" class="group bg-slate-900 border border-slate-800 rounded-xl p-6 hover:border-green-500/50 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-green-500/10 rounded-xl flex items-center justify-center group-hover:bg-green-500/20 transition">
                    <i class="fas fa-chart-bar text-green-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Hasil</h3>
                    <p class="text-gray-500 text-sm">Lihat rekomendasi</p>
                </div>
                <i class="fas fa-arrow-right text-gray-600 ml-auto group-hover:text-green-400 transition"></i>
            </div>
        </a>
    </div>

    <!-- Recent Applications -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Pengajuan Terbaru</h3>
        @php
            $recentApps = \App\Models\ScholarshipApplication::with('student.user')
                ->latest()
                ->take(5)
                ->get();
        @endphp
        
        @if($recentApps->isEmpty())
            <p class="text-gray-500 text-sm">Belum ada pengajuan.</p>
        @else
            <div class="space-y-3">
                @foreach($recentApps as $app)
                <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600/20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ $app->student->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $app->application_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/10 text-yellow-400',
                            'verified' => 'bg-blue-500/10 text-blue-400',
                            'assessed' => 'bg-green-500/10 text-green-400',
                            'rejected' => 'bg-red-500/10 text-red-400',
                        ];
                    @endphp
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColors[$app->application_status] ?? 'bg-gray-500/10 text-gray-400' }}">
                        {{ ucfirst($app->application_status) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
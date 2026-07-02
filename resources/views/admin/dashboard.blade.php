@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-red-600/10 to-pink-600/10 border border-red-500/20 rounded-2xl p-6">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, Administrator!</h1>
        <p class="text-gray-400">Pantau seluruh aktivitas sistem beasiswa dari sini.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-400"></i>
                </div>
                <span class="text-2xl font-bold">{{ $stats['totalUsers'] }}</span>
            </div>
            <p class="text-gray-500 text-xs">Total Pengguna</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-purple-400"></i>
                </div>
                <span class="text-2xl font-bold">{{ $stats['totalStudents'] }}</span>
            </div>
            <p class="text-gray-500 text-xs">Total Mahasiswa</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-yellow-400"></i>
                </div>
                <span class="text-2xl font-bold">{{ $stats['totalApplications'] }}</span>
            </div>
            <p class="text-gray-500 text-xs">Total Pengajuan</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-400"></i>
                </div>
                <span class="text-2xl font-bold">{{ $stats['totalAssessments'] }}</span>
            </div>
            <p class="text-gray-500 text-xs">Total Assessment</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-red-400"></i>
                </div>
                <span class="text-2xl font-bold">{{ $stats['totalRejections'] }}</span>
            </div>
            <p class="text-gray-500 text-xs">Total Penolakan</p>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                <span class="text-sm font-medium">Menunggu Verifikasi</span>
                <span class="ml-auto text-xl font-bold">{{ $stats['pendingApplications'] }}</span>
            </div>
            <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-400" style="width: {{ $stats['totalApplications'] > 0 ? ($stats['pendingApplications'] / $stats['totalApplications']) * 100 : 0 }}%"></div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                <span class="text-sm font-medium">Terverifikasi</span>
                <span class="ml-auto text-xl font-bold">{{ $stats['verifiedApplications'] }}</span>
            </div>
            <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-400" style="width: {{ $stats['totalApplications'] > 0 ? ($stats['verifiedApplications'] / $stats['totalApplications']) * 100 : 0 }}%"></div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                <span class="text-sm font-medium">Assessment Selesai</span>
                <span class="ml-auto text-xl font-bold">{{ $stats['assessedApplications'] }}</span>
            </div>
            <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-green-400" style="width: {{ $stats['totalApplications'] > 0 ? ($stats['assessedApplications'] / $stats['totalApplications']) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Recommendation Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center gap-4">
            <div class="w-14 h-14 bg-green-500/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-400">Direkomendasikan</p>
                <p class="text-3xl font-bold text-green-400">{{ $stats['recommended'] }}</p>
            </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center gap-4">
            <div class="w-14 h-14 bg-red-500/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle text-red-400 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-400">Tidak Direkomendasikan</p>
                <p class="text-3xl font-bold text-red-400">{{ $stats['notRecommended'] }}</p>
            </div>
        </div>
    </div>

    <!-- Recent & Top -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Applications -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-yellow-400"></i> Pengajuan Terbaru
            </h3>
            @if($recentApplications->isEmpty())
                <p class="text-gray-500 text-sm">Belum ada pengajuan.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentApplications as $app)
                    <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
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

        <!-- Top Recommendations -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-400"></i> Top Rekomendasi
            </h3>
            @if($topResults->isEmpty())
                <p class="text-gray-500 text-sm">Belum ada hasil assessment.</p>
            @else
                <div class="space-y-3">
                    @foreach($topResults as $index => $result)
                    <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-medium">{{ $result->assessment->scholarshipApplication->student->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $result->assessment->scholarshipApplication->student->student_number }}</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-green-400">{{ $result->eligibility_score }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
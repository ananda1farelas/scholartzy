@extends('layouts.dashboard')

@section('title', 'Verifikasi Pengajuan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Verifikasi Pengajuan</h2>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1 w-fit">
        <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-2 rounded-lg text-sm font-medium transition bg-yellow-600/20 text-yellow-400">
            Menunggu ({{ $applications->count() }})
        </button>
        <button onclick="switchTab('processed')" id="tab-processed" class="px-6 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white">
            Sudah Diproses ({{ $verifiedApplications->count() }})
        </button>
    </div>

    <!-- Pending -->
    <div id="content-pending" class="space-y-4">
        @if($applications->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-400">Semua Pengajuan Sudah Diverifikasi</h3>
                <p class="text-gray-500 text-sm mt-2">Tidak ada pengajuan yang menunggu verifikasi.</p>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="min-w-[800px] sm:min-w-0 px-4 sm:px-0">
                        <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">NIM</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Tanggal</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Dokumen</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($applications as $app)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-sm">{{ $app->student->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $app->student->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $app->student->student_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $app->application_date->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded text-xs font-medium">
                                        {{ $app->documents->count() }} file
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('staff.verification.show', $app->application_id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-sm font-medium transition">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Processed -->
    <div id="content-processed" class="space-y-4 hidden">
        @if($verifiedApplications->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                <i class="fas fa-inbox text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-400">Belum Ada Pengajuan Diproses</h3>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="min-w-[800px] sm:min-w-0 px-4 sm:px-0">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-800">
                                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Tanggal Verifikasi</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($verifiedApplications as $app)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-sm">{{ $app->student->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $app->student->student_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'verified' => 'bg-blue-500/10 text-blue-400',
                                            'assessed' => 'bg-green-500/10 text-green-400',
                                            'rejected' => 'bg-red-500/10 text-red-400',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColors[$app->application_status] ?? 'bg-gray-500/10 text-gray-400' }}">
                                        {{ ucfirst($app->application_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $app->updated_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('staff.verification.show', $app->application_id) }}" 
                                       class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                        Detail <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    if (tab === 'pending') {
        document.getElementById('content-pending').classList.remove('hidden');
        document.getElementById('content-processed').classList.add('hidden');
        document.getElementById('tab-pending').classList.add('bg-yellow-600/20', 'text-yellow-400');
        document.getElementById('tab-pending').classList.remove('text-gray-400');
        document.getElementById('tab-processed').classList.remove('bg-blue-600/20', 'text-blue-400');
        document.getElementById('tab-processed').classList.add('text-gray-400');
    } else {
        document.getElementById('content-pending').classList.add('hidden');
        document.getElementById('content-processed').classList.remove('hidden');
        document.getElementById('tab-processed').classList.add('bg-blue-600/20', 'text-blue-400');
        document.getElementById('tab-processed').classList.remove('text-gray-400');
        document.getElementById('tab-pending').classList.remove('bg-yellow-600/20', 'text-yellow-400');
        document.getElementById('tab-pending').classList.add('text-gray-400');
    }
}
</script>
@endpush
@endsection
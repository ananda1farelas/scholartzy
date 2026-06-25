@extends('layouts.dashboard')

@section('title', 'Pengajuan Beasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Monitoring Pengajuan Beasiswa</h2>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2">
        <button onclick="filterStatus('all')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-red-600/20 text-red-400" data-filter="all">
            Semua ({{ $applications->count() }})
        </button>
        <button onclick="filterStatus('pending')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white" data-filter="pending">
            Pending ({{ $applications->where('application_status', 'pending')->count() }})
        </button>
        <button onclick="filterStatus('verified')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white" data-filter="verified">
            Verified ({{ $applications->where('application_status', 'verified')->count() }})
        </button>
        <button onclick="filterStatus('assessed')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white" data-filter="assessed">
            Assessed ({{ $applications->where('application_status', 'assessed')->count() }})
        </button>
        <button onclick="filterStatus('rejected')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white" data-filter="rejected">
            Rejected ({{ $applications->where('application_status', 'rejected')->count() }})
        </button>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">ID</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Tanggal</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Verifikator</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Skor</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($applications as $app)
                    <tr class="hover:bg-slate-800/50 transition app-row" data-status="{{ $app->application_status }}">
                        <td class="px-6 py-4 text-sm text-gray-400">#{{ $app->application_id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $app->student->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $app->student->student_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $app->application_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
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
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $app->verifier?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($app->assessment?->result)
                                <span class="font-bold {{ $app->assessment->result->eligibility_score >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $app->assessment->result->eligibility_score }}
                                </span>
                            @else
                                <span class="text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-800 rounded text-xs text-gray-400">
                                {{ $app->documents->count() }} file
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterStatus(status) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter === status) {
            btn.classList.add('bg-red-600/20', 'text-red-400');
            btn.classList.remove('text-gray-400');
        } else {
            btn.classList.remove('bg-red-600/20', 'text-red-400');
            btn.classList.add('text-gray-400');
        }
    });

    // Filter rows
    document.querySelectorAll('.app-row').forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
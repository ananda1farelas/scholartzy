@extends('layouts.dashboard')

@section('title', 'Assessment Beasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Assessment Beasiswa</h2>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1 w-fit">
        <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-2 rounded-lg text-sm font-medium transition bg-blue-600/20 text-blue-400">
            Menunggu ({{ $pendingAssessments->count() }})
        </button>
        <button onclick="switchTab('completed')" id="tab-completed" class="px-6 py-2 rounded-lg text-sm font-medium transition text-gray-400 hover:text-white">
            Selesai ({{ $completedAssessments->count() }})
        </button>
    </div>

    <!-- Pending -->
    <div id="content-pending" class="space-y-4">
        @if($pendingAssessments->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-400">Tidak Ada Pengajuan Menunggu</h3>
                <p class="text-gray-500 text-sm mt-2">Semua pengajuan yang terverifikasi sudah di-assess.</p>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($pendingAssessments as $app)
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 flex items-center justify-between hover:border-blue-500/30 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-600/20 rounded-full flex items-center justify-center">
                            <span class="font-bold">{{ substr($app->student->full_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $app->student->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $app->student->student_number }} • {{ $app->student->study_program }}</p>
                            <p class="text-xs text-gray-600 mt-1">Diverifikasi: {{ $app->updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('staff.assessment.create', $app->application_id) }}" 
                       class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i class="fas fa-calculator"></i> Assessment
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Completed -->
    <div id="content-completed" class="space-y-4 hidden">
        @if($completedAssessments->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
                <i class="fas fa-inbox text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-400">Belum Ada Assessment</h3>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-800">
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Skor</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($completedAssessments as $app)
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
                                    <span class="text-lg font-bold {{ ($app->assessment->result->eligibility_score ?? 0) >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $app->assessment->result->eligibility_score ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($app->assessment?->result)
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $app->assessment->result->eligibility_status === 'recommended' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                            {{ $app->assessment->result->eligibility_status === 'recommended' ? 'Direkomendasikan' : 'Tidak Direkomendasikan' }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('staff.results.show', $app->assessment->result->result_id) }}" 
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
        document.getElementById('content-completed').classList.add('hidden');
        document.getElementById('tab-pending').classList.add('bg-blue-600/20', 'text-blue-400');
        document.getElementById('tab-pending').classList.remove('text-gray-400');
        document.getElementById('tab-completed').classList.remove('bg-green-600/20', 'text-green-400');
        document.getElementById('tab-completed').classList.add('text-gray-400');
    } else {
        document.getElementById('content-pending').classList.add('hidden');
        document.getElementById('content-completed').classList.remove('hidden');
        document.getElementById('tab-completed').classList.add('bg-green-600/20', 'text-green-400');
        document.getElementById('tab-completed').classList.remove('text-gray-400');
        document.getElementById('tab-pending').classList.remove('bg-blue-600/20', 'text-blue-400');
        document.getElementById('tab-pending').classList.add('text-gray-400');
    }
}
</script>
@endpush
@endsection
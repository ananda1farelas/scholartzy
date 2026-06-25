@extends('layouts.dashboard')

@section('title', 'Hasil Penilaian')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Hasil Penilaian Fuzzy Mamdani</h2>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 bg-green-500/10 text-green-400 rounded-lg text-xs font-medium">
                <i class="fas fa-check-circle mr-1"></i> Direkomendasikan: {{ $sortedResults->where('eligibility_status', 'recommended')->count() }}
            </span>
            <span class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs font-medium">
                <i class="fas fa-times-circle mr-1"></i> Tidak Direkomendasikan: {{ $sortedResults->where('eligibility_status', 'not_recommended')->count() }}
            </span>
        </div>
    </div>

    @if($sortedResults->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <i class="fas fa-chart-bar text-gray-600 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-400">Belum Ada Hasil Assessment</h3>
            <p class="text-gray-500 text-sm mt-2">Lakukan assessment terlebih dahulu untuk melihat hasil.</p>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Peringkat</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Skor Kelayakan</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Assessor</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($sortedResults as $index => $result)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="px-6 py-4">
                                <span class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-sm font-bold {{ $index < 3 ? 'text-yellow-400' : 'text-gray-400' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold">{{ substr($result->assessment->scholarshipApplication->student->full_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm">{{ $result->assessment->scholarshipApplication->student->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $result->assessment->scholarshipApplication->student->student_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-2 bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $result->eligibility_score >= 60 ? 'bg-green-500' : 'bg-red-500' }}" 
                                             style="width: {{ $result->eligibility_score }}%"></div>
                                    </div>
                                    <span class="font-bold {{ $result->eligibility_score >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $result->eligibility_score }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $result->eligibility_status === 'recommended' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                    {{ $result->eligibility_status === 'recommended' ? 'Direkomendasikan' : 'Tidak Direkomendasikan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $result->assessment->staff->name }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('staff.results.show', $result->result_id) }}" 
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
@endsection
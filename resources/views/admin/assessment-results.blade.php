@extends('layouts.dashboard')

@section('title', 'Hasil Assessment')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Hasil Assessment Fuzzy Mamdani</h2>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 bg-green-500/10 text-green-400 rounded-lg text-xs font-medium">
                Direkomendasikan: {{ $results->where('eligibility_status', 'recommended')->count() }}
            </span>
            <span class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs font-medium">
                Tidak: {{ $results->where('eligibility_status', 'not_recommended')->count() }}
            </span>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Rank</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">IPK</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Penghasilan</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Tanggungan</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Prestasi</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Rumah</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Skor Akhir</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($results as $index => $result)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold
                                {{ $index < 3 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-800 text-gray-400' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold">{{ substr($result->assessment->scholarshipApplication->student->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $result->assessment->scholarshipApplication->student->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $result->assessment->scholarshipApplication->student->student_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $result->assessment->ipk_score }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">Rp{{ number_format($result->assessment->total_family_income, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $result->assessment->dependents_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $result->assessment->achievement_score }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $result->assessment->house_condition_score }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $result->eligibility_score >= 60 ? 'bg-green-500' : 'bg-red-500' }}" 
                                         style="width: {{ $result->eligibility_score }}%"></div>
                                </div>
                                <span class="font-bold {{ $result->eligibility_score >= 60 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $result->eligibility_score }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ $result->eligibility_status === 'recommended' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ $result->eligibility_status === 'recommended' ? 'Direkomendasikan' : 'Tidak' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@extends('layouts.dashboard')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Data Mahasiswa</h2>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 bg-blue-500/10 text-blue-400 rounded-lg text-xs font-medium">
                Total: {{ $students->count() }}
            </span>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Mahasiswa</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">NIM</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Program Studi</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Semester</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Pengajuan</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Orang Tua</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($students as $student)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold">{{ substr($student->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $student->student_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $student->study_program }}</td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $student->semester }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-purple-500/10 text-purple-400 rounded text-xs font-medium">
                                {{ $student->scholarshipApplications->count() }} pengajuan
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->parentGuardian)
                                <span class="px-2 py-1 bg-green-500/10 text-green-400 rounded text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i> Lengkap
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-500/10 text-red-400 rounded text-xs font-medium">
                                    <i class="fas fa-times mr-1"></i> Belum
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
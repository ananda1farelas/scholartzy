@extends('layouts.dashboard')

@section('title', 'Data Orang Tua')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Data Orang Tua / Wali</h2>
        <span class="px-3 py-1.5 bg-purple-500/10 text-purple-400 rounded-lg text-xs font-medium">
            Total: {{ $parents->count() }}
        </span>
    </div>

    <div class="grid gap-4">
        @foreach($parents as $parent)
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-purple-400"></i>
                    </div>
                    <div>
                        <p class="font-semibold">{{ $parent->student->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $parent->student->student_number }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-slate-800 rounded-lg text-xs text-gray-400">
                    {{ $parent->dependents_count }} Tanggungan
                </span>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                @if($parent->father_name)
                <div class="bg-slate-800/50 rounded-lg p-3">
                    <p class="text-xs text-blue-400 font-medium mb-2"><i class="fas fa-male mr-1"></i>Ayah</p>
                    <p class="text-sm">{{ $parent->father_name }}</p>
                    <p class="text-xs text-gray-500">{{ $parent->father_occupation ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rp{{ number_format($parent->father_income ?? 0, 0, ',', '.') }}</p>
                </div>
                @endif

                @if($parent->mother_name)
                <div class="bg-slate-800/50 rounded-lg p-3">
                    <p class="text-xs text-pink-400 font-medium mb-2"><i class="fas fa-female mr-1"></i>Ibu</p>
                    <p class="text-sm">{{ $parent->mother_name }}</p>
                    <p class="text-xs text-gray-500">{{ $parent->mother_occupation ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rp{{ number_format($parent->mother_income ?? 0, 0, ',', '.') }}</p>
                </div>
                @endif

                @if($parent->guardian_name)
                <div class="bg-slate-800/50 rounded-lg p-3">
                    <p class="text-xs text-yellow-400 font-medium mb-2"><i class="fas fa-user-shield mr-1"></i>Wali</p>
                    <p class="text-sm">{{ $parent->guardian_name }}</p>
                    <p class="text-xs text-gray-500">{{ $parent->guardian_occupation ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Rp{{ number_format($parent->guardian_income ?? 0, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Total Penghasilan: <strong class="text-white">Rp{{ number_format(($parent->father_income ?? 0) + ($parent->mother_income ?? 0) + ($parent->guardian_income ?? 0), 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
@extends('layouts.dashboard')

@section('title', 'Status Pengajuan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Status Pengajuan Beasiswa</h2>
        <a href="{{ route('student.apply') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-plus mr-2"></i>Ajukan Baru
        </a>
    </div>

    @if($applications->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <i class="fas fa-inbox text-gray-600 text-5xl mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-400">Belum Ada Pengajuan</h3>
            <p class="text-gray-500 text-sm mt-2">Anda belum pernah mengajukan beasiswa.</p>
            <a href="{{ route('student.apply') }}" class="inline-block mt-4 text-blue-400 hover:text-blue-300 font-medium">
                Ajukan Sekarang <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    @else
        @foreach($applications as $application)
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pengajuan #{{ $application->application_id }}</p>
                    <p class="font-semibold">{{ $application->application_date->format('d F Y') }}</p>
                </div>
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'],
                        'verified' => ['label' => 'Terverifikasi', 'color' => 'bg-blue-500/10 text-blue-400 border-blue-500/20'],
                        'assessed' => ['label' => 'Assessment Selesai', 'color' => 'bg-green-500/10 text-green-400 border-green-500/20'],
                        'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-500/10 text-red-400 border-red-500/20'],
                    ];
                    $status = $statusConfig[$application->application_status] ?? ['label' => $application->application_status, 'color' => 'bg-gray-500/10 text-gray-400'];
                @endphp
                <span class="px-3 py-1.5 rounded-full text-xs font-medium border {{ $status['color'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            <!-- Timeline -->
            <div class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-check text-green-400 text-xs"></i>
                        </div>
                        <span class="text-xs text-gray-500 mt-1">Diajukan</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ in_array($application->application_status, ['verified', 'assessed']) ? 'bg-blue-500' : 'bg-slate-700' }}"></div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full {{ in_array($application->application_status, ['verified', 'assessed']) ? 'bg-blue-500/20' : 'bg-slate-800' }} flex items-center justify-center">
                            <i class="fas {{ in_array($application->application_status, ['verified', 'assessed']) ? 'fa-check text-blue-400' : 'fa-clock text-gray-600' }} text-xs"></i>
                        </div>
                        <span class="text-xs {{ in_array($application->application_status, ['verified', 'assessed']) ? 'text-blue-400' : 'text-gray-500' }} mt-1">Verifikasi</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $application->application_status === 'assessed' ? 'bg-green-500' : 'bg-slate-700' }}"></div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full {{ $application->application_status === 'assessed' ? 'bg-green-500/20' : 'bg-slate-800' }} flex items-center justify-center">
                            <i class="fas {{ $application->application_status === 'assessed' ? 'fa-check text-green-400' : 'fa-hourglass text-gray-600' }} text-xs"></i>
                        </div>
                        <span class="text-xs {{ $application->application_status === 'assessed' ? 'text-green-400' : 'text-gray-500' }} mt-1">Assessment</span>
                    </div>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="px-6 py-4 bg-slate-950/50 border-t border-slate-800">
                <h4 class="text-sm font-semibold mb-3">Dokumen Unggahan</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($application->documents as $doc)
                        @php
                            $docLabels = [
                                'transcript' => 'Transkrip',
                                'family_card' => 'KK',
                                'income_proof' => 'Penghasilan',
                                'house_photo' => 'Foto Rumah',
                                'achievement_certificate' => 'Prestasi',
                            ];
                        @endphp
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" 
                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs transition">
                            <i class="fas fa-file text-blue-400"></i>
                            {{ $docLabels[$doc->document_type] ?? $doc->document_type }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Hasil Assessment -->
            @if($application->assessment?->result)
            <div class="px-6 py-4 bg-gradient-to-r from-green-600/10 to-blue-600/10 border-t border-slate-800">
                <h4 class="text-sm font-semibold mb-3 text-green-400"><i class="fas fa-chart-line mr-2"></i>Hasil Assessment</h4>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-slate-800/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Skor Kelayakan</p>
                        <p class="text-2xl font-bold text-white">{{ $application->assessment->result->eligibility_score }}</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Status Rekomendasi</p>
                        <p class="text-lg font-bold {{ $application->assessment->result->eligibility_status === 'recommended' ? 'text-green-400' : 'text-red-400' }}">
                            {{ $application->assessment->result->eligibility_status === 'recommended' ? 'Direkomendasikan' : 'Tidak Direkomendasikan' }}
                        </p>
                    </div>
                    <div class="bg-slate-800/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="text-sm font-medium">{{ $application->assessment->result->generated_at->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Detail Assessment -->
                <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="text-center p-2 bg-slate-800/30 rounded">
                        <p class="text-xs text-gray-500">IPK</p>
                        <p class="font-bold">{{ $application->assessment->ipk_score }}</p>
                    </div>
                    <div class="text-center p-2 bg-slate-800/30 rounded">
                        <p class="text-xs text-gray-500">Penghasilan</p>
                        <p class="font-bold">Rp{{ number_format($application->assessment->total_family_income, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-2 bg-slate-800/30 rounded">
                        <p class="text-xs text-gray-500">Tanggungan</p>
                        <p class="font-bold">{{ $application->assessment->dependents_count }}</p>
                    </div>
                    <div class="text-center p-2 bg-slate-800/30 rounded">
                        <p class="text-xs text-gray-500">Prestasi</p>
                        <p class="font-bold">{{ $application->assessment->achievement_score }}</p>
                    </div>
                    <div class="text-center p-2 bg-slate-800/30 rounded">
                        <p class="text-xs text-gray-500">Rumah</p>
                        <p class="font-bold">{{ $application->assessment->house_condition_score }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($application->notes)
            <div class="px-6 py-3 bg-slate-950/30 border-t border-slate-800">
                <p class="text-xs text-gray-500"><i class="fas fa-sticky-note mr-1"></i>Catatan: {{ $application->notes }}</p>
            </div>
            @endif
        </div>
        @endforeach
    @endif
</div>
@endsection
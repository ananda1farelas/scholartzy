<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ScholarshipApplication;
use App\Models\SemesterGpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ScholarshipController extends Controller
{
    public function apply()
    {
        // Load relasi parentGuardian!
        $student = Auth::user()->load('student.parentGuardian')->student;
        
        if (!$student || !$student->parentGuardian) {
            return redirect()->route('student.profile')
                ->with('error', 'Lengkapi profil dan data orang tua terlebih dahulu!');
        }

        $currentSemester = $student->semester;
        $requiredSemesters = $currentSemester - 1;
        
        if ($requiredSemesters < 1) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum memiliki riwayat semester untuk mengajukan beasiswa.');
        }

        $existingApplication = $student->scholarshipApplications()
            ->whereIn('application_status', ['pending', 'verified'])
            ->latest()
            ->first();

        $existingGpas = $student->semesterGpas()->pluck('gpa', 'semester_number')->toArray();

        return view('student.apply', compact(
            'student',           // <-- Pastikan $student dikirim ke view
            'existingApplication', 
            'requiredSemesters', 
            'currentSemester',
            'existingGpas'
        ));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        $existing = $student->scholarshipApplications()
            ->whereIn('application_status', ['pending', 'verified'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang sedang diproses!');
        }

        $requiredSemesters = $student->semester - 1;

        // 1. Validasi Super Simpel (Tanpa validasi input tanggungan)
        $rules = [
            'documents' => ['required', 'array'],
            'documents.transcript' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.family_card' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.income_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            
            'house_photos' => ['required', 'array', 'min:4', 'max:5'], 
            'house_photos.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
            
            'cert_files' => ['nullable', 'array', 'max:10'],
            'cert_files.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        for ($i = 1; $i <= $requiredSemesters; $i++) {
            $rules["gpa_semester_$i"] = ['required', 'numeric', 'min:0', 'max:4'];
        }

        $validated = $request->validate($rules);

        // KITA HAPUS LOGIKA UPDATE TANGGUNGAN DI SINI KARENA AMBIL DARI DATABASE

        $totalGpa = 0;
        for ($i = 1; $i <= $requiredSemesters; $i++) {
            $gpaValue = $request->input("gpa_semester_$i");
            SemesterGpa::updateOrCreate(
                ['student_id' => $student->student_id, 'semester_number' => $i],
                ['gpa' => $gpaValue]
            );
            $totalGpa += $gpaValue;
        }

        $ipkKumulatif = round($totalGpa / $requiredSemesters, 2);

        $application = ScholarshipApplication::create([
            'student_id' => $student->student_id,
            'application_date' => now(),
            'application_status' => 'pending',
            'notes' => strip_tags($validated['notes'] ?? null),
        ]);

        // 2. Simpan Dokumen Utama
        $mainDocs = ['transcript', 'family_card', 'income_proof'];
        foreach ($mainDocs as $type) {
            if (isset($validated['documents'][$type])) {
                $path = $validated['documents'][$type]->store("applications/{$application->application_id}", 'public');
                ApplicationDocument::create([
                    'application_id' => $application->application_id,
                    'document_type' => $type,
                    'file_path' => $path,
                ]);
            }
        }

        // 3. Simpan Foto Rumah (Dari Array Input)
        if ($request->hasFile('house_photos')) {
            foreach ($request->file('house_photos') as $file) {
                $path = $file->store("applications/{$application->application_id}/house", 'public');
                ApplicationDocument::create([
                    'application_id' => $application->application_id,
                    'document_type' => 'house_photo',
                    'file_path' => $path,
                ]);
            }
        }

        // 4. Simpan Sertifikat (Dari Array Multiple Input)
        if ($request->hasFile('cert_files')) {
            foreach ($request->file('cert_files') as $file) {
                $path = $file->store("applications/{$application->application_id}/certificates", 'public');
                ApplicationDocument::create([
                    'application_id' => $application->application_id,
                    'document_type' => 'achievement_certificate',
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('student.status')->with('success', 
            "Pengajuan berhasil dikirim! IPK Kumulatif Anda: $ipkKumulatif. Menunggu verifikasi staff.");
    }

    public function status()
    {
        $student = Auth::user()->student;
        
        // Clear cache untuk relasi
        $student?->loadMissing(['scholarshipApplications.assessment.result']);
        
        $applications = $student?->scholarshipApplications()
            ->with([
                'documents', 
                'assessment.result',
                'assessment.staff',
                'verifier',
            ])
            ->latest()
            ->get() ?? collect();

        $semesterGpas = $student?->semesterGpas()->orderBy('semester_number')->get() ?? collect();

        return view('student.status', compact('applications', 'semesterGpas'));
    }
}
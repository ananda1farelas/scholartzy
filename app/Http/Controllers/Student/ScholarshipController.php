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
        $student = Auth::user()->student;
        
        if (!$student || !$student->parentGuardian) {
            return redirect()->route('student.profile')
                ->with('error', 'Lengkapi profil dan data orang tua terlebih dahulu sebelum mengajukan beasiswa!');
        }

        // Hitung berapa semester yang HARUS diisi
        // Semester 4 = isi IPK semester 1,2,3 (semester yang sudah lewat)
        $currentSemester = $student->semester;
        $requiredSemesters = $currentSemester - 1; // Semester yang sudah selesai
        
        if ($requiredSemesters < 1) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum memiliki riwayat semester untuk mengajukan beasiswa.');
        }

        // Cek apakah sudah ada pengajuan aktif
        $existingApplication = $student->scholarshipApplications()
            ->whereIn('application_status', ['pending', 'verified', 'assessed'])
            ->latest()
            ->first();

        // Ambil IPK yang sudah pernah diinput
        $existingGpas = $student->semesterGpas()->pluck('gpa', 'semester_number')->toArray();

        return view('student.apply', compact(
            'existingApplication', 
            'requiredSemesters', 
            'currentSemester',
            'existingGpas'
        ));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        // Cek apakah sudah ada pengajuan aktif
        $existing = $student->scholarshipApplications()
            ->whereIn('application_status', ['pending', 'verified', 'assessed'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki pengajuan yang sedang diproses!');
        }

        $currentSemester = $student->semester;
        $requiredSemesters = $currentSemester - 1;

        // Build validation rules untuk IPK per semester
        $gpaRules = [];
        $gpaMessages = [];
        for ($i = 1; $i <= $requiredSemesters; $i++) {
            $gpaRules["gpa_semester_$i"] = ['required', 'numeric', 'min:0', 'max:4'];
            $gpaMessages["gpa_semester_$i.required"] = "IPK Semester $i wajib diisi";
            $gpaMessages["gpa_semester_$i.min"] = "IPK Semester $i minimal 0";
            $gpaMessages["gpa_semester_$i.max"] = "IPK Semester $i maksimal 4";
        }

        $validated = $request->validate(array_merge([
            'documents' => ['required', 'array'],
            'documents.transcript' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.family_card' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.income_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'documents.house_photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'documents.achievement_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], $gpaRules), $gpaMessages);

        // Simpan IPK per semester
        $totalGpa = 0;
        for ($i = 1; $i <= $requiredSemesters; $i++) {
            $gpaValue = $request->input("gpa_semester_$i");
            
            SemesterGpa::updateOrCreate(
                [
                    'student_id' => $student->student_id,
                    'semester_number' => $i,
                ],
                [
                    'gpa' => $gpaValue,
                ]
            );
            
            $totalGpa += $gpaValue;
        }

        // Hitung IPK Kumulatif (rata-rata)
        $ipkKumulatif = round($totalGpa / $requiredSemesters, 2);

        // Buat pengajuan
        $application = ScholarshipApplication::create([
            'student_id' => $student->student_id,
            'application_date' => now(),
            'application_status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Simpan dokumen
        $documentTypes = [
            'transcript' => 'Transkrip Nilai',
            'family_card' => 'Kartu Keluarga',
            'income_proof' => 'Bukti Penghasilan',
            'house_photo' => 'Foto Rumah',
            'achievement_certificate' => 'Sertifikat Prestasi',
        ];

        foreach ($validated['documents'] as $type => $file) {
            if (!$file) continue;

            $path = $file->store("applications/{$application->application_id}", 'public');

            ApplicationDocument::create([
                'application_id' => $application->application_id,
                'document_type' => $type,
                'file_path' => $path,
                'uploaded_at' => now(),
            ]);
        }

        return redirect()->route('student.status')->with('success', 
            "Pengajuan berhasil dikirim! IPK Kumulatif Anda: $ipkKumulatif. Menunggu verifikasi staff.");
    }

    public function status()
    {
        $student = Auth::user()->student;
        $applications = $student?->scholarshipApplications()
            ->with([
                'documents', 
                'assessment.result',           // Hasil assessment
                'assessment.staff',              // Siapa staff yang assess
                'verifier',                       // Siapa yang verify
                'student.semesterGpas'            // IPK per semester
            ])
            ->latest()
            ->get() ?? collect();

        $semesterGpas = $student?->semesterGpas()->orderBy('semester_number')->get() ?? collect();

        return view('student.status', compact('applications', 'semesterGpas'));
    }
}
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $applications = ScholarshipApplication::with(['student.user', 'documents'])
            ->where('application_status', 'pending')
            ->latest()
            ->get();

        $verifiedApplications = ScholarshipApplication::with(['student.user', 'documents'])
            ->whereIn('application_status', ['verified', 'rejected', 'assessed'])
            ->latest()
            ->get();

        return view('staff.verification', compact('applications', 'verifiedApplications'));
    }

    public function show($id)
    {
        $application = ScholarshipApplication::with(['student.user', 'student.parentGuardian', 'documents'])
            ->findOrFail($id);

        return view('staff.verification-detail', compact('application'));
    }

    public function verify(Request $request, $id)
    {
        $application = ScholarshipApplication::with(['student.parentGuardian', 'documents', 'student.semesterGpas'])->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:500'],
            'house_condition_score' => ['nullable', 'in:0,100'], // Ambil nilai kelayakan rumah
        ]);

        $application->update([
            'application_status' => $validated['status'],
            'verified_by' => auth()->user()->user_id,
            'notes' => $validated['notes'] ?? $application->notes,
        ]);

        // JIKA DIVERIFIKASI, SISTEM MENGISI DATA ASSESSMENT
        if ($validated['status'] === 'verified') {
            
            $pg = $application->student->parentGuardian;
            $totalIncome = ($pg->father_income ?? 0) + ($pg->mother_income ?? 0) + ($pg->guardian_income ?? 0);
            
            $gpas = $application->student->semesterGpas;
            $ipkScore = $gpas->isNotEmpty() ? round($gpas->avg('gpa'), 2) : 0;
            
            $achievementScore = $application->documents->where('document_type', 'achievement_certificate')->count();

            \App\Models\Assessment::updateOrCreate(
                ['application_id' => $id],
                [
                    'staff_id' => auth()->user()->user_id,
                    'assessment_date' => now(),
                    'ipk_score' => $ipkScore,
                    'total_family_income' => $totalIncome,
                    'dependents_count' => $pg->dependents_count ?? 0,
                    'achievement_score' => $achievementScore,
                    'house_condition_score' => $validated['house_condition_score'] ?? 0,
                ]
            );
        }

        $message = $validated['status'] === 'verified' 
            ? 'Dokumen Valid! Data siap. Silakan ke menu Assessment untuk menjalankan AI Mamdani.' 
            : 'Pengajuan ditolak secara permanen.';

        return redirect()->route('staff.verification')->with('success', $message);
    }
}
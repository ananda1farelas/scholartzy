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
        $application = ScholarshipApplication::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            'application_status' => $validated['status'],
            'verified_by' => auth()->user()->user_id,
            'notes' => $validated['notes'] ?? $application->notes,
        ]);

        $message = $validated['status'] === 'verified' 
            ? 'Pengajuan berhasil diverifikasi!' 
            : 'Pengajuan ditolak.';

        return redirect()->route('staff.verification')->with('success', $message);
    }
}
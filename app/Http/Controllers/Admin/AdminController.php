<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentResult;
use App\Models\ScholarshipApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalStudents' => Student::count(),
            'totalApplications' => ScholarshipApplication::count(),
            'pendingApplications' => ScholarshipApplication::where('application_status', 'pending')->count(),
            'verifiedApplications' => ScholarshipApplication::where('application_status', 'verified')->count(),
            'assessedApplications' => ScholarshipApplication::where('application_status', 'assessed')->count(),
            'totalAssessments' => AssessmentResult::count(),
            'recommended' => AssessmentResult::where('eligibility_status', 'recommended')->count(),
            'notRecommended' => AssessmentResult::where('eligibility_status', 'not_recommended')->count(),
        ];

        $recentApplications = ScholarshipApplication::with('student.user')
            ->latest()
            ->take(5)
            ->get();

        $topResults = AssessmentResult::with('assessment.scholarshipApplication.student')
            ->where('eligibility_status', 'recommended')
            ->orderByDesc('eligibility_score')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'topResults'));
    }

    public function users()
    {
        $users = User::with('student')
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'user_password' => ['required', 'min:6'],
            'user_role' => ['required', 'in:admin,staff,student'],
            'user_status' => ['required', 'in:active,inactive'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_password' => Hash::make($validated['user_password']),
            'user_role' => $validated['user_role'],
            'user_status' => $validated['user_status'],
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->user_id . ',user_id'],
            'user_role' => ['required', 'in:admin,staff,student'],
            'user_status' => ['required', 'in:active,inactive'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->user_id === auth()->user()->user_id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus!');
    }

    public function students()
    {
        $students = Student::with(['user', 'parentGuardian', 'scholarshipApplications'])
            ->latest()
            ->get();

        return view('admin.students', compact('students'));
    }

    public function applications()
    {
        $applications = ScholarshipApplication::with(['student.user', 'documents', 'assessment.result', 'verifier'])
            ->latest()
            ->get();

        return view('admin.applications', compact('applications'));
    }

    public function assessmentResults()
    {
        $results = AssessmentResult::with(['assessment.scholarshipApplication.student.user', 'assessment.staff'])
            ->orderByDesc('eligibility_score')
            ->get();

        return view('admin.assessment-results', compact('results'));
    }
}
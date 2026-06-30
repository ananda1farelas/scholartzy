<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $parentGuardian = $student?->parentGuardian;

        return view('student.profile', compact('student', 'parentGuardian'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:100', 'unique:students,student_number,' . ($user->student?->student_id ?? 'NULL') . ',student_id'],
            'full_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'phone_number' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string'],
            'study_program' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        if ($user->student) {
            $user->student->update($validated);
        } else {
            $validated['user_id'] = $user->user_id;
            Student::create($validated);
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateParent(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return back()->with('error', 'Lengkapi profil mahasiswa terlebih dahulu!');
        }

        // Validasi: Ayah & Ibu wajib, Guardian opsional
        $rules = [
            'father_name' => ['required', 'string', 'max:100'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_income' => ['nullable', 'numeric', 'min:0'],
            'father_phone_number' => ['nullable', 'string', 'max:15'],
            'father_address' => ['nullable', 'string'],
            
            'mother_name' => ['required', 'string', 'max:100'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_income' => ['nullable', 'numeric', 'min:0'],
            'mother_phone_number' => ['nullable', 'string', 'max:15'],
            'mother_address' => ['nullable', 'string'],
            
            // Guardian: semua nullable
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'guardian_occupation' => ['nullable', 'string', 'max:100'],
            'guardian_income' => ['nullable', 'numeric', 'min:0'],
            'guardian_phone_number' => ['nullable', 'string', 'max:15'],
            'guardian_address' => ['nullable', 'string'],
            
            'dependents_count' => ['required', 'integer', 'min:0'],
        ];

        $validated = $request->validate($rules);

        // Hapus guardian fields kalau tidak dicentang
        if (!$request->has('has_guardian') || empty($validated['guardian_name'])) {
            $validated['guardian_name'] = null;
            $validated['guardian_occupation'] = null;
            $validated['guardian_income'] = null;
            $validated['guardian_phone_number'] = null;
            $validated['guardian_address'] = null;
        }

        if ($student->parentGuardian) {
            $student->parentGuardian->update($validated);
        } else {
            $validated['student_id'] = $student->student_id;
            ParentGuardian::create($validated);
        }

        return back()->with('success', 'Data orang tua/wali berhasil disimpan!');
    }
}
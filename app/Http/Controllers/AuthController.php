<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ========== LOGIN ==========
    public function showLoginForm()
    {
        return redirect('/')->with('showLogin', true);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->user_password)) {
            $guard = match($user->user_role) {
                'admin' => 'admin',
                'staff' => 'staff',
                'student' => 'student',
                default => 'web',
            };

            // LOGIN TANPA REMEMBER ME (hapus parameter true)
            Auth::guard($guard)->login($user);

            session(["active_guard_$guard" => true]);
            session(['last_active_guard' => $guard]);

            $request->session()->regenerate();

            return match ($user->user_role) {
                'admin' => redirect()->intended('/admin/dashboard'),
                'staff' => redirect()->intended('/staff/dashboard'),
                'student' => redirect()->intended('/student/dashboard'),
                default => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // ========== REGISTER ==========
    public function showRegisterForm()
    {
        return redirect('/')->with('showRegister', true);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
            'student_number' => ['required', 'string', 'max:100', 'unique:students,student_number'],
            'full_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'phone_number' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string'],
            'study_program' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_password' => Hash::make($validated['password']),
            'user_role' => 'student',
            'user_status' => 'active',
        ]);

        Student::create([
            'user_id' => $user->user_id,
            'student_number' => $validated['student_number'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'study_program' => $validated['study_program'],
            'semester' => $validated['semester'],
        ]);

        Auth::guard('student')->login($user);
        session(['active_guard_student' => true]);
        session(['last_active_guard' => 'student']);

        return redirect('/student/dashboard')->with('success', 'Registrasi berhasil!');
    }

    // ========== LOGOUT ==========
    public function logout(Request $request)
    {
        $guard = session('last_active_guard', 'web');

        // 1. Logout dari guard yang aktif (hapus key login_XXX dari session)
        Auth::guard($guard)->logout();

        // 2. Hapus session marker guard ini aja, JANGAN invalidate semua session
        session()->forget([
            "active_guard_$guard",
            'last_active_guard',
        ]);

        // 3. Regenerate token untuk keamanan, tapi JANGAN invalidate session
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout.');
    }

    // ========== SWITCH ROLE ==========
    public function switchRole($role)
    {
        $guard = match($role) {
            'admin' => 'admin',
            'staff' => 'staff',
            'student' => 'student',
            default => 'web',
        };

        if (!Auth::guard($guard)->check()) {
            return redirect('/')->with('showLogin', true)->with('error', "Silakan login sebagai $role terlebih dahulu.");
        }

        session(['last_active_guard' => $guard]);
        session(["active_guard_$guard" => true]);

        return match ($role) {
            'admin' => redirect('/admin/dashboard'),
            'staff' => redirect('/staff/dashboard'),
            'student' => redirect('/student/dashboard'),
            default => redirect('/'),
        };
    }
}
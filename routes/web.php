<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ScholarshipController;
use App\Http\Controllers\Staff\VerificationController;
use App\Http\Controllers\Staff\AssessmentController;
use App\Http\Controllers\Staff\ResultController;
use App\Http\Controllers\Admin\AdminController;

// ========== LANDING PAGE ==========
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ========== AUTH ==========
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Switch Role
Route::get('/switch/{role}', [AuthController::class, 'switchRole'])
    ->middleware('auth:admin,staff,student,web')
    ->name('switch.role');


// ========== STUDENT ==========
Route::middleware(['auth:student', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', function () {
        $student = auth('student')->user()->student;
        $latestApplication = $student?->scholarshipApplications()->latest()->first();
        return view('student.dashboard', compact('latestApplication'));
    })->name('student.dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('student.profile');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('student.profile.update');
    Route::post('/profile/parent', [ProfileController::class, 'updateParent'])->name('student.parent.update');

    Route::get('/apply', [ScholarshipController::class, 'apply'])->name('student.apply');
    Route::post('/apply', [ScholarshipController::class, 'store'])->name('student.apply.store');
    Route::get('/status', [ScholarshipController::class, 'status'])->name('student.status');
});


// ========== STAFF ==========
Route::middleware(['auth:staff', 'role:staff'])->prefix('staff')->group(function () {
    
    Route::get('/dashboard', function () {
        $pendingCount = \App\Models\ScholarshipApplication::where('application_status', 'pending')->count();
        $verifiedCount = \App\Models\ScholarshipApplication::where('application_status', 'verified')->count();
        $assessedCount = \App\Models\ScholarshipApplication::where('application_status', 'assessed')->count();
        $totalApplications = \App\Models\ScholarshipApplication::count();
        
        return view('staff.dashboard', compact('pendingCount', 'verifiedCount', 'assessedCount', 'totalApplications'));
    })->name('staff.dashboard');

    Route::get('/verification', [VerificationController::class, 'index'])->name('staff.verification');
    Route::get('/verification/{id}', [VerificationController::class, 'show'])->name('staff.verification.show');
    Route::post('/verification/{id}', [VerificationController::class, 'verify'])->name('staff.verification.process');

    Route::get('/assessment', [AssessmentController::class, 'index'])->name('staff.assessment');
    Route::get('/assessment/{applicationId}', [AssessmentController::class, 'create'])->name('staff.assessment.create');
    Route::post('/assessment/{applicationId}', [AssessmentController::class, 'store'])->name('staff.assessment.store');

    Route::get('/results', [ResultController::class, 'index'])->name('staff.results');
    Route::get('/results/{resultId}', [ResultController::class, 'show'])->name('staff.results.show');
});


// ========== ADMIN ==========
Route::middleware(['auth:admin', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::post('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::get('/parents', function () {
        $parents = \App\Models\ParentGuardian::with('student.user')->latest()->get();
        return view('admin.parents', compact('parents'));
    })->name('admin.parents');
    
    Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications');
    Route::get('/assessment-results', [AdminController::class, 'assessmentResults'])->name('admin.assessment-results');
});


// ========== FILE ACCESS ==========
Route::middleware('auth:admin,staff,student,web')->get('/file/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404, 'File tidak ditemukan');
    }
    
    $mimeType = mime_content_type($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"'
    ]);
})->where('path', '.*')->name('file.access');
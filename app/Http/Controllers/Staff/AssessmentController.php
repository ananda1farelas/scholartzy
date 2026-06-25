<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        // Pengajuan yang sudah diverifikasi tapi belum di-assess
        $pendingAssessments = ScholarshipApplication::with(['student.user', 'assessment'])
            ->where('application_status', 'verified')
            ->latest()
            ->get();

        // Pengajuan yang sudah di-assess
        $completedAssessments = ScholarshipApplication::with(['student.user', 'assessment.result'])
            ->where('application_status', 'assessed')
            ->latest()
            ->get();

        return view('staff.assessment', compact('pendingAssessments', 'completedAssessments'));
    }

    public function create($applicationId)
    {
        $application = ScholarshipApplication::with(['student.user', 'student.parentGuardian', 'student.semesterGpas'])
            ->where('application_status', 'verified')
            ->findOrFail($applicationId);

        return view('staff.assessment-form', compact('application'));
    }

    public function store(Request $request, $applicationId)
    {
        $application = ScholarshipApplication::with('student.parentGuardian')
            ->where('application_status', 'verified')
            ->findOrFail($applicationId);

        $validated = $request->validate([
            'ipk_score' => ['required', 'numeric', 'min:0', 'max:4'],
            'total_family_income' => ['required', 'numeric', 'min:0'],
            'dependents_count' => ['required', 'integer', 'min:0'],
            'achievement_score' => ['required', 'integer', 'min:0', 'max:100'],
            'house_condition_score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Ambil data dari parent guardian kalo ada
        $parentGuardian = $application->student->parentGuardian;
        $totalIncome = ($parentGuardian?->father_income ?? 0) 
                     + ($parentGuardian?->mother_income ?? 0) 
                     + ($parentGuardian?->guardian_income ?? 0);
        
        $dependents = $parentGuardian?->dependents_count ?? $validated['dependents_count'];

        // Create assessment
        $assessment = Assessment::create([
            'application_id' => $applicationId,
            'staff_id' => auth()->user()->user_id,
            'assessment_date' => now(),
            'ipk_score' => $validated['ipk_score'],
            'total_family_income' => $totalIncome > 0 ? $totalIncome : $validated['total_family_income'],
            'dependents_count' => $dependents,
            'achievement_score' => $validated['achievement_score'],
            'house_condition_score' => $validated['house_condition_score'],
        ]);

        // Run Fuzzy Mamdani
        $result = $this->calculateFuzzyMamdani($assessment);

        // Create result
        AssessmentResult::create([
            'assessment_id' => $assessment->assessment_id,
            'eligibility_score' => $result['score'],
            'eligibility_status' => $result['status'],
            'generated_at' => now(),
        ]);

        // Update application status
        $application->update(['application_status' => 'assessed']);

        return redirect()->route('staff.results')->with('success', 'Assessment berhasil! Skor kelayakan: ' . $result['score']);
    }

    private function calculateFuzzyMamdani(Assessment $assessment): array
    {
        // Fuzzification
        $ipkMembership = $this->fuzzifyIPK($assessment->ipk_score);
        $incomeMembership = $this->fuzzifyIncome($assessment->total_family_income);
        $dependentsMembership = $this->fuzzifyDependents($assessment->dependents_count);
        $achievementMembership = $this->fuzzifyAchievement($assessment->achievement_score);
        $houseMembership = $this->fuzzifyHouse($assessment->house_condition_score);

        // Inference Rules (simplified - 3 rules for demo)
        // Rule 1: IF IPK tinggi AND income rendah AND tanggungan banyak THEN layak tinggi
        // Rule 2: IF IPK rendah AND income tinggi THEN layak rendah
        // Rule 3: IF prestasi tinggi OR rumah buruk THEN layak sedang

        // Calculate firing strength for each rule
        $rule1 = min($ipkMembership['tinggi'], $incomeMembership['rendah'], $dependentsMembership['banyak']);
        $rule2 = min($ipkMembership['rendah'], $incomeMembership['tinggi']);
        $rule3 = max($achievementMembership['tinggi'], $houseMembership['buruk']);

        // Aggregate output (centroid method simplified)
        $layakTinggi = $rule1;
        $layakRendah = $rule2;
        $layakSedang = max($rule3, min($layakTinggi, 0.5));

        // Defuzzification (Weighted Average - simplified centroid)
        $output = ($layakTinggi * 85 + $layakSedang * 60 + $layakRendah * 30) / 
                  ($layakTinggi + $layakSedang + $layakRendah + 0.001);

        $score = round(max(0, min(100, $output)), 2);

        return [
            'score' => $score,
            'status' => $score >= 60 ? 'recommended' : 'not_recommended',
        ];
    }

    private function fuzzifyIPK(float $ipk): array
    {
        return [
            'rendah' => max(0, min(1, (2.5 - $ipk) / 1.5)),
            'sedang' => max(0, min(($ipk - 1.5) / 1, (3.5 - $ipk) / 1)),
            'tinggi' => max(0, min(1, ($ipk - 2.5) / 1.5)),
        ];
    }

    private function fuzzifyIncome(float $income): array
    {
        // Income in millions, assume ranges: low < 2M, medium 2-5M, high > 5M
        $incomeM = $income / 1000000;
        return [
            'rendah' => max(0, min(1, (2 - $incomeM) / 2)),
            'sedang' => max(0, min(($incomeM - 1) / 2, (5 - $incomeM) / 2)),
            'tinggi' => max(0, min(1, ($incomeM - 3) / 2)),
        ];
    }

    private function fuzzifyDependents(int $count): array
    {
        return [
            'sedikit' => max(0, min(1, (2 - $count) / 2)),
            'sedang' => max(0, min(($count - 1) / 2, (5 - $count) / 2)),
            'banyak' => max(0, min(1, ($count - 3) / 2)),
        ];
    }

    private function fuzzifyAchievement(int $score): array
    {
        return [
            'rendah' => max(0, min(1, (40 - $score) / 40)),
            'sedang' => max(0, min(($score - 20) / 30, (70 - $score) / 30)),
            'tinggi' => max(0, min(1, ($score - 50) / 50)),
        ];
    }

    private function fuzzifyHouse(int $score): array
    {
        // Score 0-100, lower = worse condition
        return [
            'buruk' => max(0, min(1, (40 - $score) / 40)),
            'sedang' => max(0, min(($score - 20) / 30, (70 - $score) / 30)),
            'baik' => max(0, min(1, ($score - 50) / 50)),
        ];
    }
}
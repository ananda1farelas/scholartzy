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
        $pendingAssessments = ScholarshipApplication::with(['student.user', 'assessment'])
            ->where('application_status', 'verified')
            ->latest()
            ->get();

        $completedAssessments = ScholarshipApplication::with(['student.user', 'assessment.result'])
            ->where('application_status', 'assessed')
            ->latest()
            ->get();

        return view('staff.assessment', compact('pendingAssessments', 'completedAssessments'));
    }

    public function create($applicationId)
    {
        // Load assessment yang sudah dibuat otomatis saat verifikasi
        $application = ScholarshipApplication::with(['student.user', 'assessment', 'documents'])
            ->where('application_status', 'verified')
            ->findOrFail($applicationId);

        return view('staff.assessment-form', compact('application'));
    }

    public function store(Request $request, $applicationId)
    {
        $application = ScholarshipApplication::where('application_status', 'verified')->findOrFail($applicationId);
        $assessment = Assessment::where('application_id', $applicationId)->firstOrFail();

        // LANGSUNG JALANKAN AI MAMDANI (Semua data sudah komplit di DB)
        $result = $this->calculateFuzzyMamdani($assessment);

        AssessmentResult::create([
            'assessment_id' => $assessment->assessment_id,
            'eligibility_score' => $result['score'],
            'eligibility_status' => $result['status'],
            'generated_at' => now(),
        ]);

        $application->update(['application_status' => 'assessed']);

        return redirect()->route('staff.results')->with('success', 'AI Mamdani berhasil dijalankan! Skor Kelayakan: ' . $result['score']);
    }

    private function calculateFuzzyMamdani(Assessment $assessment): array
    {
        $muIpk = $this->fuzzifyIPK($assessment->ipk_score);
        $muGaji = $this->fuzzifyIncome($assessment->total_family_income);
        $muTanggungan = $this->fuzzifyDependents($assessment->dependents_count);
        $muPrestasi = $this->fuzzifyAchievement($assessment->achievement_score);
        $muRumah = $this->fuzzifyHouse($assessment->house_condition_score);

        $rules = $this->get72Rules();
        $outputArea = ['rendah' => 0, 'sedang' => 0, 'tinggi' => 0];

        foreach ($rules as $rule) {
            $alpha = min(
                $muIpk[$rule['ipk']],
                $muGaji[$rule['income']],
                $muTanggungan[$rule['dependents']],
                $muPrestasi[$rule['achievement']],
                $muRumah[$rule['house']]
            );
            if ($alpha > 0) {
                $outputArea[$rule['output']] = max($outputArea[$rule['output']], $alpha);
            }
        }

        $pembilang = 0;
        $penyebut = 0;

        for ($z = 0; $z <= 100; $z++) {
            $muRendah = $z <= 50 ? (50 - $z) / 50 : 0;
            $muSedang = $z <= 25 ? 0 : ($z <= 50 ? ($z - 25) / 25 : ($z <= 75 ? (75 - $z) / 25 : 0));
            $muTinggi = $z <= 50 ? 0 : ($z - 50) / 50;

            $clipRendah = min($outputArea['rendah'], $muRendah);
            $clipSedang = min($outputArea['sedang'], $muSedang);
            $clipTinggi = min($outputArea['tinggi'], $muTinggi);

            $muZ = max($clipRendah, $clipSedang, $clipTinggi);

            $pembilang += ($muZ * $z);
            $penyebut += $muZ;
        }

        $score = $penyebut > 0 ? round($pembilang / $penyebut, 2) : 0;

        return [
            'score' => $score,
            'status' => $score >= 60 ? 'recommended' : 'not_recommended',
        ];
    }

    private function fuzzifyIPK(float $ipk): array {
        return [
            'rendah' => max(0, min(1, (2.75 - $ipk) / 0.25)),
            'sedang' => max(0, min(($ipk - 2.5) / 0.5, (3.5 - $ipk) / 0.5)),
            'tinggi' => max(0, min(1, ($ipk - 3.25) / 0.25)),
        ];
    }

    private function fuzzifyIncome(float $income): array {
        $incomeM = $income / 1000000;
        return [
            'rendah' => $incomeM <= 2.6 ? 1 : max(0, (3.9 - $incomeM) / 1.3),
            'sedang' => $incomeM <= 2.6 ? 0 : ($incomeM <= 3.9 ? ($incomeM - 2.6) / 1.3 : max(0, (5.2 - $incomeM) / 1.3)),
            'tinggi' => $incomeM <= 3.9 ? 0 : min(1, ($incomeM - 3.9) / 1.3),
        ];
    }

    private function fuzzifyDependents(int $count): array {
        return [
            'sedikit' => $count <= 2 ? 1 : max(0, 3 - $count),
            'banyak' => $count <= 2 ? 0 : min(1, $count - 2),
        ];
    }

    private function fuzzifyAchievement(int $count): array {
        return [
            'kosong' => $count <= 0 ? 1 : max(0, 1 - $count),
            'terisi' => $count <= 0 ? 0 : min(1, $count),
        ];
    }

    private function fuzzifyHouse(int $score): array {
        return [
            'layak' => max(0, min(1, (50 - $score) / 50)),
            'tidak_layak' => max(0, min(1, ($score - 50) / 50)),
        ];
    }

    private function get72Rules(): array {
        return [
            ['no' => 1, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 2, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 3, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 4, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 5, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 6, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 7, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 8, 'ipk' => 'rendah', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 9, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'rendah'],
            ['no' => 10, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 11, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 12, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 13, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 14, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 15, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 16, 'ipk' => 'rendah', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 17, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'rendah'],
            ['no' => 18, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 19, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'rendah'],
            ['no' => 20, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 21, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'rendah'],
            ['no' => 22, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 23, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 24, 'ipk' => 'rendah', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 25, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 26, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 27, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 28, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 29, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 30, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 31, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 32, 'ipk' => 'sedang', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'tinggi'],
            ['no' => 33, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 34, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 35, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 36, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 37, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 38, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 39, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 40, 'ipk' => 'sedang', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 41, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'rendah'],
            ['no' => 42, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 43, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 44, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 45, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 46, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 47, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 48, 'ipk' => 'sedang', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 49, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 50, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 51, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 52, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'tinggi'],
            ['no' => 53, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 54, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'tinggi'],
            ['no' => 55, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 56, 'ipk' => 'tinggi', 'income' => 'rendah', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'tinggi'],
            ['no' => 57, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 58, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 59, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 60, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 61, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 62, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 63, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 64, 'ipk' => 'tinggi', 'income' => 'sedang', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'tinggi'],
            ['no' => 65, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 66, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'rendah'],
            ['no' => 67, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 68, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'sedikit', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 69, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'tidak_layak', 'output' => 'sedang'],
            ['no' => 70, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'kosong', 'house' => 'layak', 'output' => 'sedang'],
            ['no' => 71, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'tidak_layak', 'output' => 'tinggi'],
            ['no' => 72, 'ipk' => 'tinggi', 'income' => 'tinggi', 'dependents' => 'banyak', 'achievement' => 'terisi', 'house' => 'layak', 'output' => 'sedang'],
        ];
    }
}
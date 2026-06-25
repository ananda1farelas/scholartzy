<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AssessmentResult;
use App\Models\ScholarshipApplication;

class ResultController extends Controller
{
    public function index()
    {
        $results = AssessmentResult::with(['assessment.scholarshipApplication.student.user', 'assessment.staff'])
            ->latest()
            ->get();

        // Sort by score descending
        $sortedResults = $results->sortByDesc('eligibility_score');

        return view('staff.results', compact('sortedResults'));
    }

    public function show($resultId)
    {
        $result = AssessmentResult::with(['assessment.scholarshipApplication.student.user', 'assessment.scholarshipApplication.documents'])
            ->findOrFail($resultId);

        return view('staff.result-detail', compact('result'));
    }
}
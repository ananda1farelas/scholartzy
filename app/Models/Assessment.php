<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'assessment_id';

    protected $fillable = [
        'application_id',
        'staff_id',
        'assessment_date',
        'ipk_score',
        'total_family_income',
        'dependents_count',
        'achievement_score',
        'house_condition_score',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'ipk_score' => 'decimal:2',
            'total_family_income' => 'decimal:2',
        ];
    }

    public function scholarshipApplication()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function result()
    {
        return $this->hasOne(AssessmentResult::class, 'assessment_id');
    }
}
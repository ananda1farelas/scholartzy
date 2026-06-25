<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'result_id';

    protected $table = 'assessment_results';

    protected $fillable = [
        'assessment_id',
        'eligibility_score',
        'eligibility_status',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'eligibility_score' => 'decimal:2',
            'generated_at' => 'datetime',
        ];
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
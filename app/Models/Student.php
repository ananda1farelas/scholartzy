<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'user_id',
        'student_number',
        'full_name',
        'birth_date',
        'gender',
        'phone_number',
        'address',
        'study_program',
        'semester',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parentGuardian()
    {
        return $this->hasOne(ParentGuardian::class, 'student_id');
    }

    public function scholarshipApplications()
    {
        return $this->hasMany(ScholarshipApplication::class, 'student_id');
    }

    public function semesterGpas()
    {
        return $this->hasMany(SemesterGpa::class, 'student_id')->orderBy('semester_number');
    }

    public function getCurrentGpaAttribute()
    {
        // Hitung IPK kumulatif dari semester GPAs
        $gpas = $this->semesterGpas;
        if ($gpas->isEmpty()) return 0;
        
        return round($gpas->avg('gpa'), 2);
    }
}
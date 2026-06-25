<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterGpa extends Model
{
    use HasFactory;

    protected $primaryKey = 'gpa_id';

    protected $fillable = [
        'student_id',
        'semester_number',
        'gpa',
    ];

    protected function casts(): array
    {
        return [
            'gpa' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentGuardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'parent_guardian_id';

    protected $table = 'parent_guardians';

    protected $fillable = [
        'student_id',
        'father_name',
        'father_occupation',
        'father_income',
        'father_phone_number',
        'father_address',
        'mother_name',
        'mother_occupation',
        'mother_income',
        'mother_phone_number',
        'mother_address',
        'guardian_name',          // OPSIONAL
        'guardian_occupation',    // OPSIONAL
        'guardian_income',        // OPSIONAL
        'guardian_phone_number',  // OPSIONAL
        'guardian_address',       // OPSIONAL
        'dependents_count',
    ];

    protected function casts(): array
    {
        return [
            'father_income' => 'decimal:2',
            'mother_income' => 'decimal:2',
            'guardian_income' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Cek apakah data wali diisi (opsional)
     */
    public function hasGuardian(): bool
    {
        return !empty($this->guardian_name);
    }
}
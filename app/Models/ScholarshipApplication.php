<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarshipApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'application_id';

    protected $table = 'scholarship_applications';

    protected $fillable = [
        'student_id',
        'application_date',
        'application_status',
        'notes',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    public function assessment()
    {
        return $this->hasOne(Assessment::class, 'application_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'user_password',
        'user_role',
        'user_status',
    ];

    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function verifiedApplications()
    {
        return $this->hasMany(ScholarshipApplication::class, 'verified_by');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'staff_id');
    }

    public function isAdmin()
    {
        return $this->user_role === 'admin';
    }

    public function isStaff()
    {
        return $this->user_role === 'staff';
    }

    public function isStudent()
    {
        return $this->user_role === 'student';
    }
}
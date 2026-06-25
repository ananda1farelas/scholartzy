<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'document_id';

    protected $table = 'application_documents';

    protected $fillable = [
        'application_id',
        'document_type',
        'file_path',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function scholarshipApplication()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id');
    }
}
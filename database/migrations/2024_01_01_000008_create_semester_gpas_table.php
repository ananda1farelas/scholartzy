<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_gpas', function (Blueprint $table) {
            $table->id('gpa_id');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->tinyInteger('semester_number'); // 1, 2, 3, dst
            $table->decimal('gpa', 3, 2); // 0.00 - 4.00
            $table->timestamps();
            
            // Satu student cuma bisa punya 1 IPK per semester
            $table->unique(['student_id', 'semester_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_gpas');
    }
};
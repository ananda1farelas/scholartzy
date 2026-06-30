<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->foreignId('user_id')->unique()->constrained('users', 'user_id');
            $table->string('student_number', 100)->unique();
            $table->string('full_name', 100);
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone_number', 15);
            $table->text('address');
            $table->string('study_program', 50);
            $table->smallInteger('semester');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
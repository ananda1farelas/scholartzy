<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_guardians', function (Blueprint $table) {
            $table->id('parent_guardian_id');
            $table->foreignId('student_id')->unique()->constrained('students', 'student_id');
            $table->string('father_name', 100)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->decimal('father_income', 15, 2)->nullable();
            $table->string('father_phone_number', 15)->nullable();
            $table->text('father_address')->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->decimal('mother_income', 15, 2)->nullable();
            $table->string('mother_phone_number', 15)->nullable();
            $table->text('mother_address')->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_occupation', 100)->nullable();
            $table->decimal('guardian_income', 15, 2)->nullable();
            $table->string('guardian_phone_number', 15)->nullable();
            $table->text('guardian_address')->nullable();
            $table->integer('dependents_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_guardians');
    }
};
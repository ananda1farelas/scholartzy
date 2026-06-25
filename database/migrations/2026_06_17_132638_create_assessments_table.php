<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id('assessment_id');
            $table->foreignId('application_id')->unique()->constrained('scholarship_applications', 'application_id');
            $table->foreignId('staff_id')->constrained('users', 'user_id');
            $table->date('assessment_date');
            $table->decimal('ipk_score', 3, 2)->notNull(); // 0.00 - 4.00
            $table->decimal('total_family_income', 15, 2)->notNull();
            $table->integer('dependents_count')->notNull();
            $table->tinyInteger('achievement_score')->notNull(); // 0-100
            $table->tinyInteger('house_condition_score')->notNull(); // 0-100
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
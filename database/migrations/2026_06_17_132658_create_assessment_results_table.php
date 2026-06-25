<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id('result_id');
            $table->foreignId('assessment_id')->unique()->constrained('assessments', 'assessment_id');
            $table->decimal('eligibility_score', 5, 2)->notNull(); // 0.00 - 100.00
            $table->enum('eligibility_status', ['recommended', 'not_recommended'])->notNull();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
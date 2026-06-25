<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->foreignId('application_id')->constrained('scholarship_applications', 'application_id');
            $table->enum('document_type', ['transcript', 'family_card', 'income_proof', 'house_photo', 'achievement_certificate']);
            $table->string('file_path', 255);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
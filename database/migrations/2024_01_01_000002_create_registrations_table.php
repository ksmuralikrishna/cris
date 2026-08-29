<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tablet_id');
            $table->string('full_name');
            $table->string('mobile_number');
            $table->string('emirates_id_number');
            $table->string('emirates_id_hash', 64);
            $table->string('emirates_id_image_path')->nullable();
            $table->timestamp('image_uploaded_at')->nullable();
            $table->string('nationality');
            $table->string('area_of_residence');
            $table->string('preferred_language');
            $table->enum('age_group', ['under_18', '18_24', '25_34', '35_44', '45_54', '55_plus']);
            $table->uuid('session_id');
            $table->timestamp('submitted_at');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tablet_id')->references('id')->on('tablets');
            $table->index('emirates_id_hash');
            $table->index('submitted_at');
            $table->index('nationality');
            $table->index('preferred_language');
            $table->index('tablet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};

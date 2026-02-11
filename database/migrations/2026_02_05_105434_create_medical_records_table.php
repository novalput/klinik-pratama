<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_visit_id')->constrained('health_visits')->cascadeOnDelete();
            $table->string('blood_pressure')->nullable();
            $table->string('temperature')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->text('diagnosis_icd10', 10)->nullable();
            $table->text('treatment_notes')->nullable(); // jenis tindakan: scaling, injeksi, dll
            $table->json('medications')->nullable(); // menyimpan data obat dalam format JSON, obat: x, aturan: 3
            $table->text('refferal')->nullable(); // rujukan ke dokter spesialis atau fasilitas lain
            $table->text('follow_up_plan')->nullable(); // instruksi tindak lanjut untuk pasien, edukasi atau kontrol ulang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};

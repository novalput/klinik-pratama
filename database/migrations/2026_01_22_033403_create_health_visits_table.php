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
        Schema::create('health_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete(); // Pasien
            $table->date('visit_date');
            $table->foreignId('dokter_id')->nullable()->constrained('dokter'); // Dokter yang menangani
            $table->text('complaint')->nullable(); 
            $table->enum('visit_type', ['baru', 'lama'])->default('baru');
            $table->enum('payment_type', ['bpjs', 'umum'])->default('umum');
            $table->text('consent')->nullable(); // contoh : Pasien telah diberi penjelasan tentang diagnosis, tindakan scaling, risiko ringan (gusi berdarah), manfaat, dan alternatif. Pasien memahami dan menyetujui secara lisan. Dokter: dr. Budi.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_visits');
    }
};

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
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cve_id')->references('id')->on('cves');
            $table->string('version');
            $table->string('subversion');
            $table->string('source');
            $table->string('type');
            $table->string('cvssData_version')->nullable();
            $table->string('cvssData_vectorString')->nullable();
            $table->string('cvssData_attackVector')->nullable();
            $table->string('cvssData_accessVector')->nullable();
            $table->string('cvssData_accessComplexity')->nullable();
            $table->string('cvssData_attackComplexity')->nullable();
            $table->string('cvssData_authentication')->nullable();
            $table->string('cvssData_privilegesRequired')->nullable();
            $table->string('cvssData_userInteraction')->nullable();
            $table->string('cvssData_scope')->nullable();
            $table->string('cvssData_confidentialityImpact')->nullable();
            $table->string('cvssData_integrityImpact')->nullable();
            $table->string('cvssData_availabilityImpact')->nullable();
            $table->double('cvssData_baseScore',4,2)->nullable();
            $table->string('cvssData_baseSeverity')->nullable();
            $table->double('exploitabilityScore',4,2);
            $table->double('impactScore',4,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};

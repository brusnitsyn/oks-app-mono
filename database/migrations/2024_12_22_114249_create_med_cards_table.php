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
        Schema::create('med_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('num')->nullable();
            $table->unsignedBigInteger('recipient_at');
            $table->unsignedBigInteger('extract_at');
            $table->unsignedBigInteger('closed_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignIdFor(\App\Models\MedDrugsStatus::class);
            $table->foreignIdFor(\App\Models\Disp::class)->nullable();
            $table->foreignIdFor(\App\Models\MedDrugsPeriod::class)->nullable();
            $table->foreignIdFor(\App\Models\MedCardAdditionalTreatment::class)->nullable();
            $table->foreignIdFor(\App\Models\MedCardReasonClose::class)->nullable();
            $table->foreignIdFor(\App\Models\Patient::class);
            $table->foreignIdFor(\App\Models\Lpu::class);
            $table->foreignIdFor(\App\Models\Mkb::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_cards');
    }
};

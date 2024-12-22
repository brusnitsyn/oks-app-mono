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
        Schema::create('med_card_control_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\MedCard::class);
            $table->foreignIdFor(\App\Models\ControlCallResult::class)->nullable();
            $table->foreignIdFor(\App\Models\SurveyResult::class)->nullable();
            $table->foreignIdFor(\App\Models\Survey::class)->default(1);
            $table->string('name');
            $table->unsignedBigInteger('call_at');
            $table->unsignedBigInteger('called_at')->nullable();
            $table->unsignedBigInteger('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_card_control_calls');
    }
};

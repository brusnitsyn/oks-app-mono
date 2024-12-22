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
        Schema::create('med_card_mkb_accompanyings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\MedCard::class);
            $table->foreignIdFor(\App\Models\Mkb::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_card_mkb_accompanyings');
    }
};

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
        Schema::table('med_card_control_calls', function (Blueprint $table) {
            $table->text('info')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        Schema::table('med_card_control_calls', function (Blueprint $table) {
//            $table->unsignedBigInteger('info')->nullable()->change();
//        });
    }
};

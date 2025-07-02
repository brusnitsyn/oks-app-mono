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
        Schema::table('med_cards', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class, 'creater_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('med_cards', function (Blueprint $table) {
            $table->dropColumn('creater_user_id');
        });
    }
};

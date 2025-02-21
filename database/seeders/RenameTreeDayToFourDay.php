<?php

namespace Database\Seeders;

use App\Models\MedCardControlCall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RenameTreeDayToFourDay extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $controlCalls = MedCardControlCall::where('name', '3-й день')->get();
        foreach ($controlCalls as $controlCall) {
            MedCardControlCall::withoutEvents(function () use ($controlCall) {
                $controlCall->update([
                    'name' => '4-й день'
                ]);
            });
        }
    }
}

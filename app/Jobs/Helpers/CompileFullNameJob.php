<?php

namespace App\Jobs\Helpers;

use App\Models\Patient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompileFullNameJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $patients = Patient::all();
        foreach ($patients as $patient) {
            $patient->update([
                'family' => $patient->family,
                'name' => $patient->name,
                'ot' => $patient->ot,
            ]);
        }
    }
}

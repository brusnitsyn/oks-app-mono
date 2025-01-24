<?php

namespace App\Jobs\Helpers;

use App\Models\Patient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class CompileFullNameJob implements ShouldQueue
{
    use Queueable;

    private $patients;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->patients = Patient::all();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->patients as $patient) {
            $patient->update();
        }
    }
}

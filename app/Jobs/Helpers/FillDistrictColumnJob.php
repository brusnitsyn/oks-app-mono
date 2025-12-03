<?php

namespace App\Jobs\Helpers;

use App\Models\Patient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class FillDistrictColumnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $patients;
    private $requestsPerMinute = 50; // Оставляем запас от 60
    private $delayBetweenRequests; // Задержка в секундах

    public function __construct()
    {
        $this->patients = Patient::whereNull('district')->get();
        $this->delayBetweenRequests = 60 / $this->requestsPerMinute;
    }

    public function handle(): void
    {
        foreach ($this->patients as $patient) {
            if (empty($patient->fias_objectid)) continue;
            try {
                $response = Http::timeout(30)
                    ->retry(3, 1000) // 3 попытки с задержкой 1 секунда
                    ->get(config('fias.url') . "/$patient->fias_objectid/district");

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['full_name'])) {
                        $patient->update([
                            'fias_district_objectid' => $data['objectid'],
                            'district' => $data['full_name']
                        ]);
                    }
                }

                // Пауза между запросами для соблюдения rate limit
                Sleep::for($this->delayBetweenRequests)->seconds();

            } catch (\Exception $e) {
                \Log::error('Ошибка при обработке пациента ID: ' . $patient->id, [
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }
}

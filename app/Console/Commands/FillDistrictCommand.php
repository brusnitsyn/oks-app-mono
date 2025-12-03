<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Helpers\FillDistrictColumnJob;

class FillDistrictCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fias:fill-districts
                            {--chunk-size=100 : Количество записей для обработки в одной джобе}
                            {--queue : Запустить в очереди}
                            {--sync : Запустить синхронно (без очереди)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнить district из ФИАС API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало заполнения district...');

        if ($this->option('sync')) {
            // Запуск синхронно (без очереди)
            $job = new FillDistrictColumnJob();
            $job->handle();
            $this->info('Задача выполнена синхронно.');
        } else {
            // Запуск через очередь
            $queue = $this->option('queue') ? 'default' : null;

            FillDistrictColumnJob::dispatch()
                ->onQueue($queue ?? config('queue.default'));

            $this->info('Задача добавлена в очередь.');
        }

        $this->info('Готово!');

        return Command::SUCCESS;
    }
}

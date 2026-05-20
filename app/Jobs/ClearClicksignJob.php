<?php

namespace App\Jobs;

use App\Models\Tenancy\Clicksign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearClicksignJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(
        private readonly string $dbName,
        private readonly string $city,
    ) {}

    public function handle(): void
    {
        DB::statement("SET search_path TO {$this->dbName}");
        Clicksign::where('id', '!=', '0')->delete();

        Log::info('ClearClicksignJob: registros removidos', ['city' => $this->city]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ClearClicksignJob: falha', [
            'city'  => $this->city,
            'error' => $e->getMessage(),
        ]);
    }
}

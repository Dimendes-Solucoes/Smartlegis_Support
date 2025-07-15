<?php

namespace App\Providers;

use App\Models\Transcription;
use App\Models\Webhook;
use App\Observers\TranscriptionObserver;
use App\Observers\WebhookObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transcription::observe(TranscriptionObserver::class);
        Webhook::observe(WebhookObserver::class);
    }
}

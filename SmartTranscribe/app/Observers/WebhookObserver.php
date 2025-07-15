<?php

namespace App\Observers;

use App\Jobs\WebhookCreateJob;
use App\Models\Webhook;

class WebhookObserver
{
    /**
     * Handle the Webhook "created" event.
     */
    public function created(Webhook $webhook): void
    {
        dispatch(new WebhookCreateJob($webhook));
    }

    /**
     * Handle the Webhook "updated" event.
     */
    public function updated(Webhook $webhook): void
    {
        //
    }

    /**
     * Handle the Webhook "deleted" event.
     */
    public function deleted(Webhook $webhook): void
    {
        //
    }

    /**
     * Handle the Webhook "restored" event.
     */
    public function restored(Webhook $webhook): void
    {
        //
    }

    /**
     * Handle the Webhook "force deleted" event.
     */
    public function forceDeleted(Webhook $webhook): void
    {
        //
    }
}

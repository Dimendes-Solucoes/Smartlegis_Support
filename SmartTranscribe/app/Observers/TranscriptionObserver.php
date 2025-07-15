<?php

namespace App\Observers;

use App\Jobs\SendUpdateTranscriptionToClientJob;
use App\Models\Transcription;

class TranscriptionObserver
{
    /**
     * Handle the Transcription "created" event.
     */
    public function created(Transcription $transcription): void
    {
        //
    }

    /**
     * Handle the Transcription "updated" event.
     */
    public function updated(Transcription $transcription): void
    {
        if ($transcription->isDirty('content') || $transcription->isDirty('status')) {
            if (in_array($transcription->status, [Transcription::STATUS_SUCCEEDED, Transcription::STATUS_FAILED])) {
                dispatch(new SendUpdateTranscriptionToClientJob($transcription));
            }
        }
    }

    /**
     * Handle the Transcription "deleted" event.
     */
    public function deleted(Transcription $transcription): void
    {
        //
    }

    /**
     * Handle the Transcription "restored" event.
     */
    public function restored(Transcription $transcription): void
    {
        //
    }

    /**
     * Handle the Transcription "force deleted" event.
     */
    public function forceDeleted(Transcription $transcription): void
    {
        //
    }
}

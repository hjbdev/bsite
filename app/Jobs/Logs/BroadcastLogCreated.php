<?php

namespace App\Jobs\Logs;

use App\Events\Logs\LogCreated;
use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastLogCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Log $log)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new LogCreated($this->log));
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EmailTemplate;
use App\Mail\BaseEmailMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     * 
     * @param mixed $template
     * @param Collection $users
     */
    public function __construct(
        protected mixed $template,
        protected Collection $users
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            SendSingleEmailJob::dispatch($this->template, $user);
        }
    }
}

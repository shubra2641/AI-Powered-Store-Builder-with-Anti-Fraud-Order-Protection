<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\EmailTemplate;
use App\Mail\BaseEmailMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected mixed $template,
        protected User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $templateObj = $this->template;

        if (is_array($this->template)) {
            $templateObj = new EmailTemplate($this->template);
        }

        Mail::to($this->user->email)->send(new BaseEmailMailable($templateObj, [
            'user' => $this->user
        ]));
    }
}

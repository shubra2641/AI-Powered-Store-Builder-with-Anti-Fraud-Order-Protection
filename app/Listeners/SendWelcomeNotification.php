<?php

namespace App\Listeners;

use App\Events\UserActivated;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected EmailService $emailService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(UserActivated $event): void
    {
        $this->emailService->sendTemplateEmail($event->user, 'welcome_email');
    }
}

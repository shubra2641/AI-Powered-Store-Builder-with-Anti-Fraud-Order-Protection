<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Notifications\Notification;
use App\Models\EmailTemplate;
use App\Traits\DS_SafeTemplateRenderer;
use App\Services\SettingsService;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable, DS_SafeTemplateRenderer;

    /**
     * Create a new notification instance.
     * 
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @param string $type (success, info, warning, error)
     * @param string|null $templateSlug
     * @param array $templateData
     */
    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $type = 'info',
        public ?string $templateSlug = null,
        public array $templateData = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($this->templateSlug) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $template = EmailTemplate::where('slug', $this->templateSlug)
            ->where('language_id', $notifiable->language_id ?? 1)
            ->first();

        if (!$template) {
            $template = EmailTemplate::where('slug', $this->templateSlug)->first();
        }

        if (!$template) {
            return (new MailMessage)
                ->subject($this->title)
                ->line($this->message);
        }

        $data = array_merge($this->templateData, ['user' => $notifiable]);
        
        $content = $this->safeRender($template->content, $data);
        $subject = $this->safeRender($template->subject, $data);

        return (new MailMessage)
            ->subject($subject)
            ->html($content);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'type'    => $this->type,
            'icon'    => match($this->type) {
                'success' => 'fas fa-check-circle',
                'error'   => 'fas fa-exclamation-circle',
                'warning' => 'fas fa-exclamation-triangle',
                default   => 'fas fa-info-circle',
            },
        ];
    }
}

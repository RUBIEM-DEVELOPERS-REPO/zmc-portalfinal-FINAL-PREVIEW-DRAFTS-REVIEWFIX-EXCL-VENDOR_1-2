<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $appType = ($this->application->application_type === 'accreditation') 
            ? 'Accreditation (' . strtoupper($this->application->request_type) . ')' 
            : 'Media House Registration';

        return (new MailMessage)
            ->subject('Application Submitted - ' . $this->application->reference)
            ->greeting('Dear ' . ($notifiable->name ?? 'Applicant') . ',')
            ->line('Your application for ' . $appType . ' has been successfully submitted.')
            ->line('Reference Number: ' . $this->application->reference)
            ->line('Submitted On: ' . ($this->application->submitted_at ? $this->application->submitted_at->format('d M Y H:i') : now()->format('d M Y H:i')))
            ->line('Next Step: Your application is now awaiting review. Please ensure you have made the required payment to avoid delays.')
            ->action('View My Applications', route('accreditation.home'))
            ->line('Thank you for choosing the Zimbabwe Media Commission.');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'reference' => $this->application->reference,
            'message' => 'Your application ' . $this->application->reference . ' has been submitted successfully.',
            'type' => 'application_submitted'
        ];
    }
}

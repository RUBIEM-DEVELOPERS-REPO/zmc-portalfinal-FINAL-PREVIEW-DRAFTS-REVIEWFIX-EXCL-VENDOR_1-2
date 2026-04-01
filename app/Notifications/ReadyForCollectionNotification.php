<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReadyForCollectionNotification extends Notification implements ShouldQueue
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
        $this->application->load('applicant');
        
        $type = $this->application->application_type === 'accreditation' ? 'accreditation card' : 'registration certificate';
        
        $regions = [
            'harare' => 'Harare Regional Office',
            'bulawayo' => 'Bulawayo Regional Office',
            'mutare' => 'Mutare Regional Office',
            'masvingo' => 'Masvingo Regional Office',
            'gweru' => 'Gweru Terminal',
            'chinhoyi' => 'Chinhoyi Terminal',
        ];

        $location = $this->application->collection_region 
            ? ($regions[strtolower($this->application->collection_region)] ?? ucfirst($this->application->collection_region) . ' Office') 
            : 'ZMC Head Office in Harare';

        return (new MailMessage)
            ->subject('Important: Your ZMC ' . ucwords($type) . ' is Ready for Collection')
            ->greeting('Dear ' . ($this->application->applicant?->name ?? 'Applicant') . ',')
            ->line('We are pleased to inform you that the production of your ' . $type . ' for application ' . $this->application->reference . ' is complete.')
            ->line('It is now ready for collection at our ' . $location . '.')
            ->line('Please bring a valid form of identification when you come to collect.')
            ->line('Thank you for choosing the Zimbabwe Media Commission.');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'reference' => $this->application->reference,
            'message' => 'Your ' . ($this->application->application_type === 'accreditation' ? 'accreditation card' : 'registration certificate') . ' is ready for collection at our ' . ($this->application->collection_region ? ucfirst($this->application->collection_region) : 'Harare') . ' office.',
            'type' => 'ready_for_collection'
        ];
    }
}

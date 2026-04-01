<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffOTPNotification extends Notification
{
    public function __construct(public string $otp)
    {
    }

    public function via($notifiable): array
    {
        return ['mail']; // Remove ShouldQueue to send immediately
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ZMC Portal Login OTP')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your one-time password (OTP) for logging into the ZMC Portal is:')
            ->line('**' . $this->otp . '**')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not attempt to login, please secure your account.');
    }

    public function toArray($notifiable): array
    {
        return [
            'otp' => $this->otp,
            'type' => 'staff_otp'
        ];
    }
}

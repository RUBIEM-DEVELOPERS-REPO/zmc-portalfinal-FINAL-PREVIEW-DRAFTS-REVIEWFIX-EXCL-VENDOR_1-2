<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffAccountSetupNotification extends Notification
{
    public function __construct(public string $token, public string $tempPassword)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $setupUrl = route('account.setup', ['token' => $this->token]);
        $dashboardUrl = route('staff.login');

        return (new MailMessage)
            ->subject('Welcome to ZMC Portal - Account Setup')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('An account has been created for you on the ZMC Portal.')
            ->line('Your login credentials are:')
            ->line('Email: ' . $notifiable->email)
            ->line('Temporary Password: ' . $this->tempPassword)
            ->line('Dashboard Access: ' . $dashboardUrl)
            ->line('Please click the button below to set your permanent password and complete your account setup.')
            ->action('Set Up Account', $setupUrl)
            ->line('This link will expire for security reasons.')
            ->line('After setting your password, you will need to verify your identity with an OTP each time you login.')
            ->line('If you did not expect this invitation, please ignore this email.');
    }

    public function toArray($notifiable): array
    {
        return [
            'token' => $this->token,
            'type' => 'staff_account_setup'
        ];
    }
}

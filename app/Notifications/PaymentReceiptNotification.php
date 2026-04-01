<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentReceiptNotification extends Notification implements ShouldQueue
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
        $this->application->load(['applicant', 'batch']);

        $data = [
            'application' => $this->application,
            'date' => now()->format('Y-m-d H:i'),
            'company_name' => 'Zimbabwe Media Commission',
            'company_address' => '109 Rotten Row, Harare, Zimbabwe',
            'company_email' => 'info@zmc.co.zw',
            'company_phone' => '+263 242 703351',
            'amount' => $this->application->amount_paid ?: $this->application->fee_amount ?: 0,
            'currency' => $this->application->currency ?: 'USD',
            'reference' => $this->application->paynow_reference ?? $this->application->batch?->reference ?? $this->application->receipt_number ?? 'N/A'
        ];

        $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $data);

        return (new MailMessage)
            ->subject('Payment Receipt - ' . $this->application->reference)
            ->greeting('Dear ' . ($this->application->applicant?->name ?? 'Applicant') . ',')
            ->line('Thank you for your payment. Your application ' . $this->application->reference . ' has been marked as paid and is now being processed.')
            ->line('Please find your digital receipt attached to this email.')
            ->attachData($pdf->output(), 'Receipt_' . $this->application->reference . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->line('If you have any questions, please contact our support team.');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'reference' => $this->application->reference,
            'message' => 'Your payment for application ' . $this->application->reference . ' has been confirmed. A receipt has been sent to your email.',
            'type' => 'payment_receipt'
        ];
    }
}

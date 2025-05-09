<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PurchaseRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseRequest;

    public function __construct(PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre demande d\'achat a été rejetée')
            ->line('Votre demande d\'achat n°' . $this->purchaseRequest->id . ' a été rejetée.')
            ->action('Voir la demande', route('purchase.requests.show', $this->purchaseRequest))
            ->line('Contactez votre responsable pour plus d\'informations.');
    }
}
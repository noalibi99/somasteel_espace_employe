<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PurchaseRequestSubmitted extends Notification implements ShouldQueue
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
            ->subject('Nouvelle demande d\'achat à valider')
            ->line('Une nouvelle demande d\'achat a été soumise par ' . $this->purchaseRequest->user->name)
            ->action('Voir la demande', route('purchase.requests.show', $this->purchaseRequest))
            ->line('Merci de valider ou rejeter cette demande dans les plus brefs délais.');
    }
}
<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        Log::info("Envoi email à: " . $notifiable->email);
        return (new MailMessage)
            ->subject('[Action Requise] Nouvelle demande d\'achat à valider')
            ->greeting('Bonjour ' . $notifiable->nom . ',')
            ->line('Une nouvelle demande d\'achat a été soumise par ' . $this->purchaseRequest->user->nom)
            ->line('Référence demande: #' . $this->purchaseRequest->id)
            ->line('Date: ' . $this->purchaseRequest->created_at->format('d/m/Y H:i'))
            ->action('Voir la demande', route('purchase.requests.show', $this->purchaseRequest))
            ->line('Merci de valider ou rejeter cette demande dans les plus brefs délais.')
            ->salutation('Cordialement,')
            ->line('L\'équipe ' . config('app.name'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
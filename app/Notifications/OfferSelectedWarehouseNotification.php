<?php

// app/Notifications/OfferSelectedWarehouseNotification.php
namespace App\Notifications;

use App\Models\RFQ;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferSelectedWarehouseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public RFQ $rfq;
    public Offer $selectedOffer;

    public function __construct(RFQ $rfq, Offer $selectedOffer)
    {
        $this->rfq = $rfq;
        $this->selectedOffer = $selectedOffer;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $supplierName = $this->selectedOffer->supplier->name;
        $purchaseRequest = $this->rfq->purchaseRequest;

        $mailMessage = (new MailMessage)
                    ->subject('Préparation Réception : Offre Fournisseur Sélectionnée pour RFQ #' . $this->rfq->id)
                    ->greeting('Bonjour ' . $notifiable->prénom . ',')
                    ->line('Une offre du fournisseur **' . $supplierName . '** a été sélectionnée pour la Demande de Prix (RFQ) #' . $this->rfq->id . '.')
                    ->line('Cette RFQ est issue de la demande d\'achat #' . $purchaseRequest->id . ' (Demandeur: ' . $purchaseRequest->user->nom . ').')
                    ->line('Un Bon de Commande sera prochainement généré. Veuillez vous préparer pour la réception des articles suivants :');

        foreach($this->selectedOffer->offerLines as $line) {
            if ($line->quantity_offered > 0) {
                $articleName = $line->article->designation ?? $line->description;
                $mailMessage->line('- ' . $line->quantity_offered . ' x ' . $articleName);
            }
        }

        $mailMessage->action('Voir le RFQ', route('purchase.rfqs.show', $this->rfq->id))
                    ->line('Vous serez notifié lorsque le Bon de Commande sera envoyé au fournisseur.')
                    ->salutation('Cordialement,')
                    ->line('Le Service Achat');

        return $mailMessage;
    }
}

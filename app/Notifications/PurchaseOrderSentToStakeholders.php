<?php

// app/Notifications/PurchaseOrderSentToStakeholders.php
namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderSentToStakeholders extends Notification implements ShouldQueue
{
    use Queueable;

    public PurchaseOrder $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Bon de Commande #' . $this->purchaseOrder->po_number . ' Envoyé au Fournisseur';
        $greeting = 'Bonjour ' . $notifiable->prénom . ',';
        $line1 = 'Le Bon de Commande #' . $this->purchaseOrder->po_number . ' a été envoyé au fournisseur **' . $this->purchaseOrder->supplier->name . '**.';
        $actionText = 'Voir le Bon de Commande';
        $actionUrl = route('purchase.orders.show', $this->purchaseOrder->id);

        if ($notifiable->isMagasinier()) {
            $line2 = 'Veuillez vous préparer pour la réception des articles. La date de livraison globale attendue est le ' .
                     ($this->purchaseOrder->expected_delivery_date_global ? $this->purchaseOrder->expected_delivery_date_global->format('d/m/Y') : 'non spécifiée') . '.';
        } elseif ($notifiable->id === $this->purchaseOrder->rfq->purchaseRequest->user_id) { // Si c'est le demandeur original
            $subject = 'Votre demande d\'achat a été commandée : BDC #' . $this->purchaseOrder->po_number;
            $line2 = 'Votre demande d\'achat initiale (DA #' . $this->purchaseOrder->rfq->purchaseRequest->id . ') a été traitée et une commande a été passée.';
        } else {
            // Pourrait être admin ou autre rôle
            $line2 = 'Les articles suivants ont été commandés :';
        }

        $mailMessage = (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2);

        if (!isset($line2) || (isset($line2) && $notifiable->isMagasinier())) { // Afficher les lignes d'articles pour le magasinier par exemple
             $mailMessage->line('Articles commandés :');
             foreach($this->purchaseOrder->purchaseOrderLines as $line) {
                $mailMessage->line('- ' . $line->quantity_ordered . ' x ' . ($line->article->designation ?? $line->description));
             }
        }

        $mailMessage->action($actionText, $actionUrl)
                    ->salutation('Cordialement,')
                    ->line('Le système ' . config('app.name'));

        return $mailMessage;
    }
}

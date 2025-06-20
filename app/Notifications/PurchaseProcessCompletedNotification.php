<?php

// app/Notifications/PurchaseProcessCompletedNotification.php
namespace App\Notifications;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseProcessCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public PurchaseOrder $purchaseOrder;
    public Invoice $invoice; // La dernière facture payée qui a clôturé le processus

    public function __construct(PurchaseOrder $purchaseOrder, Invoice $invoice)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $purchaseRequest = $this->purchaseOrder->rfq->purchaseRequest;
        $subject = 'Processus d\'Achat Terminé pour DA #' . $purchaseRequest->id . ' (BDC #' . $this->purchaseOrder->po_number . ')';

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Bonjour ' . $notifiable->prénom . ',')
                    ->line('Le processus d\'achat pour la demande initiale #' . $purchaseRequest->id . ' (Demandeur: ' . $purchaseRequest->user->nom . ') est maintenant terminé.')
                    ->line('Le Bon de Commande associé #' . $this->purchaseOrder->po_number . ' (Fournisseur: ' . $this->purchaseOrder->supplier->company_name . ') a été livré et la facture #' . $this->invoice->invoice_number . ' a été entièrement payée.')
                    ->line('Statut final de la Demande d\'Achat : ' . $purchaseRequest->status_label)
                    ->line('Statut final du Bon de Commande : ' . $this->purchaseOrder->status_label)
                    ->action('Voir le Bon de Commande', route('purchase.orders.show', $this->purchaseOrder->id))
                    ->salutation('Cordialement,')
                    ->line('Le Service Comptabilité');
    }
}

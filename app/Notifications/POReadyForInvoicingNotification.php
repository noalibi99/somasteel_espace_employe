<?php

// app/Notifications/POReadyForInvoicingNotification.php
namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class POReadyForInvoicingNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
                    ->subject('Action Requise : BDC #' . $this->purchaseOrder->po_number . ' est Prêt pour Facturation')
                    ->greeting('Bonjour ' . $notifiable->prénom . ',')
                    ->line('Le Bon de Commande #' . $this->purchaseOrder->po_number . ' (Fournisseur: ' . $this->purchaseOrder->supplier->name . ') a été marqué comme **Totalement Livré**.')
                    ->line('Veuillez procéder à la vérification et à l\'enregistrement de la facture fournisseur correspondante.')
                    ->action('Voir le Bon de Commande', route('purchase.orders.show', $this->purchaseOrder->id))
                    // Ou lien vers le dashboard compta pour créer la facture
                    // ->action('Créer la Facture', route('purchase.invoices.create', $this->purchaseOrder->id))
                    ->salutation('Cordialement,')
                    ->line('Le Système de Gestion des Achats');
    }
}

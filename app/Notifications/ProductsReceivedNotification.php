<?php

// app/Notifications/ProductsReceivedNotification.php
namespace App\Notifications;

use App\Models\Delivery;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductsReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Delivery $delivery;
    public PurchaseOrder $purchaseOrder;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        $this->purchaseOrder = $delivery->purchaseOrder; // Accéder au PO via la livraison
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $poStatusLabel = $this->purchaseOrder->status_label;
        $subject = 'Réception de Produits pour BDC #' . $this->purchaseOrder->po_number . ' (BL: ' . $this->delivery->delivery_reference . ')';
        $greeting = 'Bonjour ' . $notifiable->prénom . ',';

        $line1 = 'Des produits ont été réceptionnés pour le Bon de Commande #' . $this->purchaseOrder->po_number .
                 ' (Fournisseur: ' . $this->purchaseOrder->supplier->name . ').';
        $line2 = 'Référence Bon de Livraison Fournisseur: **' . $this->delivery->delivery_reference . '** (Date: ' . $this->delivery->delivery_date->format('d/m/Y') . ').';
        $line3 = 'Le statut du Bon de Commande est maintenant : **' . $poStatusLabel . '**.';

        $mailMessage = (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2)
                    ->line($line3)
                    ->line('Articles réceptionnés pour ce BL :');

        foreach($this->delivery->deliveryLines as $line) {
            if ($line->quantity_received > 0) {
                $articleName = $line->article->designation ?? $line->purchaseOrderLine->description;
                $mailMessage->line('- ' . $line->quantity_received . ' x ' . $articleName);
            }
        }

        $mailMessage->action('Voir Détails Réception', route('purchase.deliveries.show', $this->delivery->id))
                    ->line('Le service comptabilité sera notifié si la commande est prête pour facturation.')
                    ->salutation('Cordialement,')
                    ->line('Le Service Magasin');
        return $mailMessage;
    }
}

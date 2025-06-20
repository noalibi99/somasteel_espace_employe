<?php

namespace App\Notifications;

use App\Models\RFQ;
use App\Models\Supplier; // Importer le modèle Supplier
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RFQSentToSupplierNotification extends Notification implements ShouldQueue // Implémenter ShouldQueue pour l'envoi asynchrone
{
    use Queueable;

    public RFQ $rfq;
    public string $emailSubject;
    public string $emailBody;
    public Supplier $supplier; // Pour personnaliser le message avec le nom du fournisseur

    /**
     * Create a new notification instance.
     */
    public function __construct(RFQ $rfq, Supplier $supplier, string $emailSubject, string $emailBody)
    {
        $this->rfq = $rfq;
        $this->supplier = $supplier; // Stocker le fournisseur
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Remplacer les placeholders
        $parsedBody = str_replace(
            ['{supplier_name}', '{rfq_id}', '{deadline_for_offers}', '{company_name}'],
            [$this->supplier->name, $this->rfq->id, ($this->rfq->deadline_for_offers ? $this->rfq->deadline_for_offers->format('d/m/Y H:i') : 'N/A'), config('app.name')],
            $this->emailBody
        );

        $parsedSubject = str_replace(
            ['{supplier_name}', '{rfq_id}', '{company_name}'],
            [$this->supplier->name, $this->rfq->id, config('app.name')],
            $this->emailSubject
        );


        return (new MailMessage)
                    ->subject($parsedSubject)
                    ->greeting('Bonjour ' . $this->supplier->name . ',') // Ou un autre champ de contact si disponible
                    ->line('Vous avez reçu une nouvelle demande de prix (RFQ) de la part de ' . config('app.name') . '.')
                    ->line("Référence RFQ: RFQ#" . $this->rfq->id)
                    ->lineIf($this->rfq->deadline_for_offers, "Veuillez soumettre votre offre avant le : " . $this->rfq->deadline_for_offers->format('d/m/Y H:i') . ".")
                    ->line('--- Contenu du message personnalisé ---')
                    ->line(nl2br(e($parsedBody))) // nl2br pour conserver les sauts de ligne, e() pour échapper
                    ->line('--- Fin du message personnalisé ---')
                    ->line('Description de la demande d\'achat originale : ' . $this->rfq->purchaseRequest->description)
                    // ->action('Voir les détails (si un portail fournisseur existe)', url('/supplier-portal/rfq/'.$this->rfq->id)) // Exemple
                    ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
                    ->salutation('Cordialement,')
                    ->line('Le Service Achat de ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rfq_id' => $this->rfq->id,
            'message' => 'Nouvelle RFQ reçue: ' . $this->emailSubject,
        ];
    }
}

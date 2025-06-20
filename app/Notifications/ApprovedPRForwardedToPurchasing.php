<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ApprovedPRForwardedToPurchasing extends Notification implements ShouldQueue
{
    use Queueable;

    public PurchaseRequest $purchaseRequest;

    public function __construct(PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;
        // Charger les relations nécessaires ici si elles ne sont pas déjà chargées
        // pour s'assurer qu'elles sont disponibles lorsque la notification est traitée par la file d'attente.
        $this->purchaseRequest->loadMissing(['user', 'validator']);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Les relations user et validator devraient être chargées grâce au constructeur
        $requester = $this->purchaseRequest->user;
        $validator = $this->purchaseRequest->validator; // Directeur qui a validé

        // Vérification pour éviter les erreurs si les relations ne sont pas chargées (sécurité)
        $requesterName = $requester ? ($requester->nom . ' ' . $requester->prénom) : 'Demandeur inconnu';
        $validatorName = $validator ? ($validator->nom . ' ' . $validator->prénom) : 'Validateur inconnu';
        $notifiableName = $notifiable instanceof User ? $notifiable->prénom : 'Utilisateur';


        return (new MailMessage)
                    ->subject('[Action Requise] Demande d\'Achat #' . $this->purchaseRequest->id . ' approuvée et prête pour RFQ')
                    ->greeting('Bonjour ' . $notifiableName . ',') // Utiliser $notifiableName
                    ->line('La demande d\'achat #' . $this->purchaseRequest->id . ', soumise par ' . $requesterName . ', a été approuvée par ' . $validatorName . '.')
                    ->line('Description : ' . Str::limit($this->purchaseRequest->description, 100)) // Str::limit est maintenant accessible
                    ->line('Vous pouvez maintenant procéder à la création d\'une Demande de Prix (RFQ) pour cette demande.')
                    ->action('Voir la Demande d\'Achat Approuvée', route('purchase.requests.show', $this->purchaseRequest->id))
                    ->salutation('Cordialement,')
                    ->line('Le système ' . config('app.name'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Supplier extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', // Assurez-vous que c'est 'name' après votre migration de renommage
        'contact_phone',
        'contact_email',
        'company_name',
        'contact_first_name',
        'contact_last_name',
        'city',
        'country',
    ];
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Un fournisseur peut recevoir plusieurs Rfqs
    public function rfqs()
    {
        return $this->belongsToMany(Rfq::class, 'rfq_supplier');
    }


    public function routeNotificationForMail($notification)
    {
        // Retourne l'adresse e-mail du fournisseur.
        // Le système de notification utilisera cette adresse pour envoyer l'e-mail.
        return $this->contact_email;
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_name',
        'contact_first_name',
        'contact_last_name',
        'contact_phone',
        'contact_email',
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
}

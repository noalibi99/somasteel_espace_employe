<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

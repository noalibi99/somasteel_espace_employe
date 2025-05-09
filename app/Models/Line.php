<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'article_id',
        'description',
        'quantity',
        'is_new_item'
    ];
    
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}

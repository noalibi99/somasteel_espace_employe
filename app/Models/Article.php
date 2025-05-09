<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'reference',
        'designation',
        'description',
        'SN',
        'status',
        'prix'
    ];

    public function lines()
    {
        return $this->hasMany(Line::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    protected $casts = [
        'status' => 'string',
    ];
    
    public static function boot()
    {
        parent::boot();
    
        static::creating(function ($article) {
            $article->status = $article->status ?? 'pending';
        });
    }
}

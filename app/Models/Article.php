<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval'; // Si vous avez un état intermédiaire
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected'; // Si un article peut être rejeté
    const STATUS_OBSOLETE = 'obsolete';

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'user_id',
        'validator_id',
        'description',
        'motifRejet',
        'status',
        'validated_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function lines()
    {
        return $this->hasMany(Line::class);
    }

    public function rfq()
    {
        return $this->hasOne(RFQ::class);
    }

    protected $appends = ['status_label', 'status_color'];

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'processed' => 'Traitée',
        ][$this->status];
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'processed' => 'info',
        ][$this->status];
    }
}

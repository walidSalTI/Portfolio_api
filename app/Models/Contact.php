<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'address',
        'social_links',
        'portfolio_id',
        'display',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

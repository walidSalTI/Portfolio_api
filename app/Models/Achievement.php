<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'achieved_at',
        'portfolio_id',
        'display',
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('images/' . $this->image) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

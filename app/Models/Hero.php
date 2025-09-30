<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'portfolio_id',
        'display',
        'cv'
    ];
    protected $appends = ['image_url','cv_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('images/' . $this->image) : null;
    }
    public function getCvUrlAttribute()
    {
        return $this->cv ? asset('CV/' . $this->cv) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

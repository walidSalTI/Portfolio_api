<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'description',
        'level',
        'icon',
        'display',
        'portfolio_id',
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        return $this->icon ? asset('images/' . $this->icon) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

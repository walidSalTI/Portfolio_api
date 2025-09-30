<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonials extends Model
{
    protected $fillable = [
        'client_name',
        'client_position',
        'client_image',
        'qoute',
        'display',
        'portfolio_id',
    ];
    protected $appends = ['client_image_url'];
    public function getClientImageUrlAttribute(){
        return $this->client_image ? asset('images/testimonials/' . $this->client_image) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

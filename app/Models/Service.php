<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'display',
        'portfolio_id',
    ];
    protected $appends = ['icon_url'];  
    public function getIconUrlAttribute(){
        return $this->icon ? asset('images/services/' . $this->icon) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

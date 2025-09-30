<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'project_url',
        'started_at',
        'completed_at',
        'portfolio_id',
        'display',
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute(){
        return $this->image ? asset('images/projects/' . $this->image) : null;
    }
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

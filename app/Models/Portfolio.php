<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Hero;
use App\Models\About;
use App\Models\Contact;
use App\Models\Testimonials;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Achievement;
use App\Models\Message;

class Portfolio extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function hero(){
        return $this->hasOne(Hero::class);
    }
    public function about()
    {
        return $this->hasOne(About::class);
    }
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
    public function testimonials()
    {
        return $this->hasMany(Testimonials::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }


    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

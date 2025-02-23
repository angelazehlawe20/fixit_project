<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'category_id',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function portfolio()
    {
        return $this->hasOne(Portfolio::class);
    }

    public function task()
    {
        return $this->hasMany(Task::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

}

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


    public function portfolio()
    {
        // علاقة one to one مع صاحب العمل
        return $this->hasMany(Portfolio::class);
    }

    public function task()
    {
        return $this->hasMany(Task::class,'contractor_id');
    }

    public function user()
    {
        // تعني "تابع الى" ..يعني ان صاحب العمل ينتمي إلى مستخدم واحد فقط.
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

}

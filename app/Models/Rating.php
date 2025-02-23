<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'contractor_id',
        'task_id',
        'comment',
        'rate_value'
    ];

    public function contrctor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
   
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable=[
        'task_id',
        'payment_end_date',
        'price',
        'task_done_date',
        'completation_status'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
    
}

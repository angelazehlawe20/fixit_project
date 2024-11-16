<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable=[
        'task_id',
        'payment_date',
        'price',
        'end_date',
        'contract_status'
    ];

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class,'task_id');
    }
}

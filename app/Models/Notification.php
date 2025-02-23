<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'contractor_id',
        'notifiable_type',
        'notifiable_id',
        'content',
        'is_read'
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}

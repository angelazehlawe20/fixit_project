<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'contractor_id',
        'title',
        'description'
    ];

    public function image()
    {
        return $this->hasMany(Image::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }


}

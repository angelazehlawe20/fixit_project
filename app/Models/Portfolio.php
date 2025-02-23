<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable=[
        'contractor_id',
        'title'
    ];

    public function image()
    {
        return $this->hasMany(Image::class);
    }

    public function contractor()
    {
        return $this->belongsTo(contractor::class);
    }
    
}

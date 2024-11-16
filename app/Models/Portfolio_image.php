<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio_image extends Model
{
    use HasFactory;

    protected $fillable=[
        'image_id',
        'portfolio_id'
    ];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}

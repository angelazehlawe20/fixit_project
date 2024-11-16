<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable=[
        'name'
    ];

    public function task_image()
    {
        return $this->hasMany(Task_image::class);
    }

    public function portfolio_image()
    {
        return $this->hasMany(Portfolio_image::class);
    }
}

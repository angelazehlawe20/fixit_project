<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name'];

    public function images()
    {
        return $this->belongsToMany(Image::class, 'category_images', 'category_id', 'image_id');
    }
}


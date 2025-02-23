<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة في قاعدة البيانات
    protected $fillable=[
        'category_name',
    ];

    public function image()
    {
        return $this->hasOne(Image::class);
    }

    public function contractor()
    {
        return $this->hasMany(Contractor::class);
    }
}

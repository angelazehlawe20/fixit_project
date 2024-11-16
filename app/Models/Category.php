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
        'image'
    ];

    public function contractor()
    {
        // علاقة "واحد إلى متعدد" حيث يمكن لل category أن تحتوي على عدة contractor
        return $this->hasMany(Contractor::class);
    }
}

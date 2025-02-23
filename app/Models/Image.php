<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'imageable_type',
        'imageable_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
}

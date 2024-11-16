<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task_image extends Model
{
    use HasFactory;

    protected $fillable=[
        'task_id',
        'image_id'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}

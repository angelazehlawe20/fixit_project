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
        'address',
        'city',
        'country',
        'title',
        'task_status',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function task_image()
    {
        return $this->hasMany(Task_image::class);
    }

    //تعني أن النموذج الأول مرتبط بالنموذج الثاني عن طريق علاقة hasMany
    //والنموذج الثاني مرتبط بالنموذج الثالث أيضًا عن طريق علاقة hasMany
    public function portfolios()
    {
        return $this->hasManyThrough(Portfolio::class, Contractor::class);
    }

}

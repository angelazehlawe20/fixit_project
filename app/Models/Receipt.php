<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable=[
        'contract_id',
        'amount',
        'status'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}

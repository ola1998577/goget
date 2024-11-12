<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class driver extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function delivery_company(){
        return $this->belongsTo(User::class,'deliveryCompany_id');
    }
}

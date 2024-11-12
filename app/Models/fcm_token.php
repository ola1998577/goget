<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fcm_token extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function token(){
        return $this->belongsTo(token::class);
    }
}

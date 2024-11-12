<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function product(){
        return $this->belongsTo(product::class);
    }
    public function store(){
        return $this->belongsTo(store::class,'store_id');
    }

    public function token(){
        return $this->belongsTo(token::class);
    }
}

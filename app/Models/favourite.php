<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favourite extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['created_at', 'updated_at'];

    public function product(){
        return $this->belongsTo(product::class);
    }
    public function store(){
        return $this->belongsTo(store::class);
    }

    public function token(){
        return $this->belongsTo(token::class);
    }
}

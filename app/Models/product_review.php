<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_review extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['created_at', 'updated_at'];

    public function product(){
        return $this->belongsTo(product::class);
    }
}

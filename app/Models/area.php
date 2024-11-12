<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function governate(){
        return $this->belongsTo(governate::class);
    }
}

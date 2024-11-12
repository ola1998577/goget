<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $hidden = ['created_at', 'updated_at'];


    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function getTranslation($locale)
    {
        return $this->translations->where('locale', $locale)->first();
    }

 // علاقة مع جدول المتاجر (product)
 public function product()
 {
     return $this->hasMany(product::class);
 }
}

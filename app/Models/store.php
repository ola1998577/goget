<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class store extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['created_at', 'updated_at'];

    public $translatable = ['name'];
    public function product(){
        return $this->hasMany(product::class);
    }


    public function translations()
    {
        return $this->hasMany(storeTranslation::class);
    }

    public function getTranslation($field, $locale)
    {
        $translation = $this->translations->where('language', $locale)->first();
        return $translation ? $translation->$field : null;
    }


}

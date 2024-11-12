<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $hidden = ['created_at', 'updated_at'];


    public function translations()
    {
        return $this->hasMany(productTranslation::class);
    }

    public function getTranslation($field, $locale)
    {
        $translation = $this->translations->where('language', $locale)->first();
        return $translation ? $translation->$field : null;
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function colors(){
        return $this->hasMany(product_color::class);
    }

    public function sizes(){
        return $this->hasMany(product_size::class);
    }

    public function image(){
        return $this->hasMany(product_image::class);
    }
    public function reviews(){
        return $this->hasMany(product_review::class);
    }

    // public function orders()
    // {
    //     return $this->belongsToMany(order::class, 'order_products')
    //                 ->withPivot('quantity', 'price')
    //                 ->withTimestamps();
    // }

    public function orders()
    {
        return $this->belongsToMany(order::class, 'order_products')
                    ->withTimestamps();
    }

}

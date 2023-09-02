<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $hidden = [
        'images',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'address' => 'json',
        'images' => 'array',
    ];

    protected $appends = ['photos'];

    public function getPhotosAttribute()
    {
        $photos = [];
        foreach ($this->images as $image) {
            $photos[] = asset('upload/properties/' . $image);
        }
        return $photos;
    }

    public function scopeOfUser($query, $user_id)
    {
        if ($user_id) {
            return $query->where('user_id', $user_id);
        } else {
            return $query;
        }
    }

    public function scopeOfCategory($query, $category_id)
    {
        if ($category_id) {
            return $query->where('category_id', $category_id);
        } else {
            return $query;
        }
    }

    public function scopeOfCity($query, $city_id)
    {
        if ($city_id) {
            return $query->where('city_id', $city_id);
        } else {
            return $query;
        }
    }

    public function scopeOfPrice($query, $price_from, $price_to)
    {
        if ($price_from && $price_to) {
            return $query->whereBetween('price', [$price_from, $price_to]);
        } else {
            return $query;
        }
    }

    public function scopeOfSearch($query, $search)
    {
        if ($search) {
            return $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        } else {
            return $query;
        }
    }

    public function scopeOfArea($query, $area_from, $area_to)
    {
        if ($area_from && $area_to) {
            return $query->whereBetween('area', [$area_from, $area_to]);
        } else {
            return $query;
        }
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

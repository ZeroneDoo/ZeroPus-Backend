<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected 
    $guarded = ['id'],
    $casts = [
        "is_rent" => "boolean"
    ];

    public function getPhotoAttribute($value)
    {
        if($value === null) {
            return "https://mdbootstrap.com/img/Photos/Others/placeholder.jpg"; 
        }
        return url(Storage::url($value));
    }
    public function getSourceAttribute($value)
    {
        if($value !== null) {
            return url(Storage::url($value));
        }
        return $value; 
    }
    public function category()
    {
        return $this->belongsToMany(Category::class, CategoryBook::class);
    }
    public function bookmark()
    {
        return $this->hasMany(Bookmark::class);
    }
}

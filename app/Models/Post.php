<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;


class Post extends Model
{
    use Sluggable;

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sluggable(): array
    {
        return [
            'slug'=>[
                'source' => 'title'
            ]
            ];
    }

    
}

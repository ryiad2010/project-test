<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];

    // One-to-many relation: A post has many comments.
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Many-to-many relation: A post can have many tags.
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

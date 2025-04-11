<?php

namespace App\Models;

use Attribute;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'content',
        'image',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (?string $image) => $image ? Storage::url($image) : null,
        );
    }
}

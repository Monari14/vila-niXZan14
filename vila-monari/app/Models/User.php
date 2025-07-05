<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Usuários que eu sigo
    public function seguindo()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    // Usuários que me seguem
    public function seguidores()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    # Método de juntar o user com os posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likesInOthersPosts()
    {
        return $this->hasMany(Like::class, 'user_id')->whereNull('comment_id');
    }

    public function likesInOthersComments()
    {
        return $this->hasMany(Like::class, 'user_id')->whereNull('post_id');
    }


    public function likesInMyPosts()
    {
        return $this->hasManyThrough(
            Like::class,
            Post::class,
            'user_id',   // FK na tabela posts que aponta para o usuário dono do post
            'post_id',   // FK na tabela likes que aponta para o post que recebeu o like
            'id',        // PK do usuário
            'id'         // PK do post
        );
    }
    public function likesInMyComments()
    {
        return $this->hasManyThrough(
            Like::class,
            Comment::class,
            'user_id',   // FK na tabela comments que aponta para o usuário dono do comentário
            'comment_id', // FK na tabela likes que aponta para o comentário que recebeu o like
            'id',        // PK do usuário
            'id'         // PK do comentário
        );
    }
}

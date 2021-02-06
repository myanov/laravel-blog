<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const IS_ADMIN = 1;
    const IS_NORMAL = 0;
    const BANNED = 1;
    const UN_BANNED = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->save();

        return $user;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function generatePassword($password)
    {
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    public function remove()
    {
        $this->remove();
    }

    public function uploadAvatar($image)
    {
        if($image == null) {return;}

        $this->removeAvatar();

        $filename = Str::random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar()
    {
        if($this->avatar != null)
            Storage::delete('uploads/' . $this->avatar);
    }

    public function getAvatar()
    {
        if($this->avatar == null)
            return '/img/no-image.png';

        return '/uploads/' . $this->avatar;
    }

    public function makeAdmin()
    {
        $this->is_admin = User::IS_ADMIN;
        $this->save();
    }

    public function makeNormal()
    {
        $this->is_admin = User::IS_NORMAL;
        $this->save();
    }

    public function toggleAdmin($value)
    {
        if($value == null)
            return $this->makeNormal();

        return $this->makeAdmin();
    }

    public function ban()
    {
        $this->status = User::BANNED;
        $this->save();
    }

    public function unBan()
    {
        $this->status = User::UN_BANNED;
        $this->save();
    }

    public function toggleStatus($value)
    {
        if($value == null)
            return $this->unBan();

        return $this->ban();
    }
}

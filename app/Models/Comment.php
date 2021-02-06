<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    const IS_ALLOW = 1;
    const IS_DISALLOW = 0;

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function post()
    {
        return $this->hasOne(User::class);
    }

    public function allow()
    {
        $this->status = Comment::IS_ALLOW;
        $this->save();
    }

    public function disAllow()
    {
        $this->status = Comment::IS_DISALLOW;
        $this->save();
    }

    public function toggleStatus()
    {
        if($this->status == 0)
            return $this->allow();

        return $this->disAllow();
    }

    public function remove()
    {
        $this->delete();
    }
}

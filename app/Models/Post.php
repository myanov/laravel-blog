<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;
    const IS_STANDART = 0;
    const IS_FEATURED = 1;

    protected $fillable = ['title', 'content'];

    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function author()
    {
        return $this->hasOne(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'posts_tags',
            'post_id',
            'tag_id'
        );
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->save();

        return $post;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete();
    }

    public function removeImage()
    {
        if($this->image != null) {
            Storage::delete('uploads/' . $this->image);
        }
    }

    public function uploadImage($image)
    {
        if($image == null) {return;}

        $this->removeImage();
        $fileName = Str::random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $fileName);
        $this->image = $fileName;
        $this->save();
    }

    public function getImage()
    {
        if($this->image == null)
            return '/img/no-image.png';

        return '/uploads/' . $this->image;
    }

    public function setCategory($id)
    {
        if($id == null) {return;}

//        $category = Category::find($id);
//        $this->category()->save($category);
        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
        if($ids == null) {return;}

        $this->tags()->sync($ids);
    }

    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }

    public function toggleStatus($value)
    {
        if($value == null)
            return $this->setDraft();

        return $this->setPublic();
    }

    public function setFeatured()
    {
        $this->is_featured = Post::IS_FEATURED;
        $this->save();
    }

    public function setStandart()
    {
        $this->is_featured = Post::IS_STANDART;
        $this->save();
    }

    public function toggleFeatured($value)
    {
        if($value == null)
            return $this->setStandart();

        return $this->setFeatured();
    }

}

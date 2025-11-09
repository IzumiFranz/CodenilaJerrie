<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuestionTag extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'instructor_id'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function questions()
    {
        return $this->belongsToMany(QuestionBank::class, 'question_bank_tag');
    }
}
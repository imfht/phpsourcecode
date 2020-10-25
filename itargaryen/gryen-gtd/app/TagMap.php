<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagMap extends Model
{
    protected $fillable = [
        'tag_id',
        'article_id',
    ];

    public function article()
    {
        $this->belongsTo('App\Article', 'article_id');
    }

    public function tag()
    {
        $this->belongsTo('App\Tag', 'tag_id');
    }
}

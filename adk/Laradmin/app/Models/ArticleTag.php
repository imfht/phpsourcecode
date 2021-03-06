<?php

namespace App\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ArticleTag extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    /*
    * 多对多关联文章表
    * */
    public function articles()
    {
        return $this->belongsToMany('App\Models\Article', 'article_tag');
    }

}

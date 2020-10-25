<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Banner extends Eloquent
{
    protected $fillable = [
        'article_id',
        'cover',
        'weight',
        'status',
    ];

    public function article()
    {
        return $this->belongsTo('App\Article');
    }
}

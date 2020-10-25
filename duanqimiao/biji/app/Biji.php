<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Biji
 * @package App
 */
class Biji extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','book_id','title','content','published_at'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function books(){
        return $this->belongsTo('App\Book');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(){
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany('App\Comment');
    }
}

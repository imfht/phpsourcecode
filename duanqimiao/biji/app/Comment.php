<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['parent_id','biji_id','user_id','comments'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function bijis(){
        return $this->belongsTo('App\Biji');
    }


    public function users(){
        return $this->belongsTo('App\User');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collect extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','biji_id'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo('App\User');
    }
}

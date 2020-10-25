<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    /**
     * @var array
     */
    protected $fillable = ["user_id","thumb","signed_at"];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function users(){
        return $this->belongsTo('App\User');
    }
}

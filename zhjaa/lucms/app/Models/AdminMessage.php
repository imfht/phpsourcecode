<?php

namespace App\Models;

class AdminMessage extends Model
{

    protected $fillable = [
        'user_id', 'title', 'content', 'status'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'phone', 'name');
    }

    public function destroyAdminMessage($authId)
    {
        $this->delete();
    }


}

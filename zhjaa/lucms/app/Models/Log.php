<?php

namespace App\Models;

use DB;
use Auth;

class Log extends Model
{
    protected $casts = [
        'content' => 'array',
    ];

    protected $fillable = [
        'type', 'table_name', 'ip', 'content'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeTypeSearch($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    public function scopeTableNameSearch($query, $table_name)
    {
        return $query->where('table_name', '=', $table_name);
    }

    public function storeLog($input)
    {
        try {
            $this->fill($input);
            $this->user_id = $input['user_id'];
            $this->save();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            return $this->baseFailed('内部错误');
        }
    }

}

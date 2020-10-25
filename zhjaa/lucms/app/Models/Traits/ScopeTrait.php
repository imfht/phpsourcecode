<?php

namespace App\Models\Traits;

use App\Models\User;

trait ScopeTrait
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeEnable($query)
    {
        return $query->where('enable', 'T');
    }

    public function scopeEnableSearch($query, $value)
    {
        return $query->where('enable', $value);
    }

    public function scopeColumnLike($query, $column, $value)
    {
        return $query->where($column, 'like', $value . '%');
    }

    public function scopeColumnEqualSearch($query, $column, $value)
    {
        return $query->where($column, $value);
    }

    public function scopeColumnInSearch($query, $column, array $value)
    {
        return $query->whereIn($column, $value);
    }

    public function scopeUserNameSearch($query, $user_name)
    {
        $user_ids = User::columnLike('name', $user_name)->pluck('id');
        return $query->whereIn('user_id', $user_ids);
    }
}

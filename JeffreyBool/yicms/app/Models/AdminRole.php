<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminRole
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole query()
 * @mixin \Eloquent
 */
class AdminRole extends Model
{
    protected $fillable = ['admin_id', 'role_id'];
}

<?php
/**
 * Created by PhpStorm.
 * User: ADKi
 * Date: 2017/8/10 0010
 * Time: 10:37
 * @author DukeAnn
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
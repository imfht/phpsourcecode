<?php

namespace App\Models;

use App\Http\Controllers\Api\Traits\BaseResponseTrait;
use App\Models\Traits\ExcuteTrait;
use App\Models\Traits\ScopeTrait;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use ScopeTrait, ExcuteTrait,BaseResponseTrait;



}

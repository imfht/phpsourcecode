<?php

namespace App\Validates;

use App\Traits\RedisTrait;
use DB;

class  AdminMessageValidate extends Validate
{
    use RedisTrait;
    protected $message = '操作成功';
    protected $data = [];
}

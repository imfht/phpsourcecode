<?php
/**
 * Created by PhpStorm.
 * User: crazycooler
 * Date: 2017/3/25
 * Time: 17:38
 */

namespace App\Common;


use Illuminate\Support\Facades\Validator;

class StValidator
{
    public static function make($obj,array $param = [])
    {
        $validator = Validator::make($obj,$param);

        if($validator->fails()){
            throw new StException('none','bad_parameter');
        }
    }
}
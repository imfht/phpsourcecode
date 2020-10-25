<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 14:56
 */

namespace app\admin\validate;


use think\Validate;

class AdverValidate extends Validate
{

    protected $rule=[
        'advert_name'  =>  'require',

        '__token__' => 'token',

    ];
    protected $message  =   [
        'advert_name.require' => '广告名称不能为空',




    ];

}
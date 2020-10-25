<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\admin\validate;


use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title|标题'  => 'require|max:64',
    ];

}
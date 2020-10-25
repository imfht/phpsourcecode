<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Group extends Validate
{
    protected $rule = [
        'title'  => 'require',
        'name' => 'require'
    ];

    protected $message  =   [
        'title.require'      => '名称必须',
        'name.require'      => '标识必须'
    ];

    public function scenePower()
    {
        return $this->only(['id','power'])
            ->append('id', 'require')
            ->append('power', 'require');
    }
}
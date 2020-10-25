<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/3
 * Time: 14:03
 */
namespace app\first\validate;

use think\Validate;

class Carousel extends Validate{
    protected $rule=[
        'title'=>'require'
    ];
    public function sceneQuery(){
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }
    public function sceneDelete(){
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }
}
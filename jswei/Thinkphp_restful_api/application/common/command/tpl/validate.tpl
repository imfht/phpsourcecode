<?php
namespace app\{$namespace}\validate;

use think\Validate;
/**
 * Class {$_controller}
 * @package app\{$controller}\validate
 */
class {$_controller} extends Validate{
    //验证规则
    protected $rule = [];
    //验证信息
    protected $message = [];
    //场景验证规则
    public function sceneQuery(){
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }
}
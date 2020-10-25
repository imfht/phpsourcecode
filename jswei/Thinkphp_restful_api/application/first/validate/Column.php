<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/3
 * Time: 9:42
 */
namespace app\first\validate;

use think\Validate;
class Column extends Validate{
    protected $rule=[
        'id'=>'require|number',
        'title'=>'require',
        'name'=>'require'
    ];

    public function sceneQuery()
    {
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }

    public function sceneDelete()
    {
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }

    public function sceneEdit(){
        return $this->only(['id'])
            ->append('id', 'require|integer')
            ->remove('title')
            ->remove('name');
    }
}
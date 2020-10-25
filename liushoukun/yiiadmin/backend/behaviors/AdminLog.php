<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/1/4 11:55
// +----------------------------------------------------------------------
// | TITLE:
// +----------------------------------------------------------------------
namespace backend\behaviors;

use yii\base\Behavior;

class AdminLog extends Behavior
{

    public function events()
    {
       return [
           'logs'=>'logs'
       ];
    }


    public function logs($e)
    {
        //todo 实现日志

    }


}
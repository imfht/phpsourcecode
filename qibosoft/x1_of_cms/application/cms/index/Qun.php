<?php
namespace app\cms\index;

use app\common\controller\IndexBase;

/**
 *圈子接口demo示例
 *本文件可以完全删除,不影响圈子模板内容的调用.
 *详细使用方法,请查看教程  http://help.php168.com/1122037
 */
class Qun extends IndexBase
{
    /**
     * 这是其中一个方法之一,只能使用一个参数,其它参数,请用input() 比如 $id=input('id')获得
     * @param array $info 圈子的信息
     * @return string[] 要么返回数组供模板使用,要么就不要返回任何东西.
     */
    public function index($info=[]){
        $array = [
            'xxx'=>'测试变量的传递,当前圈子名称是:'.$info['title'],
        ];
        return $array;
    }
}

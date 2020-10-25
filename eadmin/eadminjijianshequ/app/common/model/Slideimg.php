<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 模型
 */
class Slideimg extends ModelBase
{


    public function getTypenameAttr($type)
    {


        $status = [1 => 'WAP', 2 => '网站', 3 => '小程序', 4 => 'APP'];

        return $status[$type];
    }

}

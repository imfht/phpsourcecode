<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 模型
 */
class Articlecate extends ModelBase
{

    public function getSubcountAttr($id)
    {

        $count = $this->setname('articlecate')->getStat(['pidstr|~' => '0|' . $id]);

        return $count;
    }

    public function getLmcountAttr($pidstr)
    {


        $realstrcount = substr_count($pidstr, '|');

        $status = [0 => '顶级栏目', 1 => '二级栏目', 2 => '三级栏目', 3 => '四级栏目', 4 => '五级栏目'];

        return $status[$realstrcount];
    }

}

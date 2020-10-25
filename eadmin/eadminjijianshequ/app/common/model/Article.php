<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 文章模型
 */
class Article extends ModelBase
{


    public function getTidnameAttr($tid)
    {
        $name = $this->setname('articlecate')->getDataValue(['id' => $tid], 'name');

        return $name;
    }
}

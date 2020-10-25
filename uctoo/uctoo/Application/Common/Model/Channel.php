<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 分类模型
 */
class Channel extends Model
{

    /**
     * 获取导航列表，支持多级导航
     * @param  boolean $field 要列出的字段
     * @return array          导航树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function lists($field = true, $tree = false)
    {
        $map = array('status' => 1);
        $list = Db::name('channel')->field($field)->where($map)->order('sort')->cache('common_nav')->select();
        return $tree ? list_to_tree($list, 'id', 'pid', '_') : $list;
    }

}

<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\cms\model;

use think\Model;

/**
 * 文章分类模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Category extends Model
{
    public function news()
    {
        return $this->hasMany('News', 'id')->bind('name');
    }
}

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
 * 文章模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class News extends Model
{
    public function user()
    {
        return $this->belongsTo('User', 'id');
    }

    public function category()
    {
        return $this->belongsTo('Category', 'id');
    }
}

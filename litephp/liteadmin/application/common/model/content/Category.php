<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 16:49
 */

namespace app\common\model\content;

use think\Model;

class Category extends Model
{
    protected $name = "content_category";

    public function parent()
    {
        return $this->belongsTo(Category::class,'pid','id');
    }
}
<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 16:48
 */

namespace app\common\model\content;

use think\Model;

class Advs extends Model
{
    protected $name = "content_advs";

    // 数据库写入使用了insert方法没有使用save，所以自动时间戳不生效！
    protected $autoWriteTimestamp = true;

    public function category(){
        return $this->belongsTo(AdvsCategory::class,'cid','id');
    }
}
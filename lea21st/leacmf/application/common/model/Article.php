<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\common\model;

use app\common\library\Rbac;
use lea21st\Auth;
use think\Model;

class Article extends Model
{

    public static $cate = [
        1 => '系统单页',
        2 => '公告'
    ];

    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 自动完成
     * @var array
     */
    protected $insert = ['create_aid', 'status' => 1];

    protected $readonly = ['cate'];

    /**
     * 设置操作人
     * @return mixed
     */
    protected function setCreateAidAttr()
    {
        return Rbac::instance()->getUserId();
    }

}
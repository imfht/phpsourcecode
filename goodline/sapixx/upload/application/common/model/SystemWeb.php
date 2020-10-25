<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 站点配置
 */
namespace app\common\model;
use think\Model;

class SystemWeb extends Model
{
    protected $pk = 'id';

    /**
     * 读取站点配置
     */
    public static function config(){
        return self::where(['id' => 1])->find();
    }
}

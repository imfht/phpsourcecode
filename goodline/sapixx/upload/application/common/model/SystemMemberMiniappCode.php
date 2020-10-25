<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 小程序提交状态
 */
namespace app\common\model;
use think\Model;

class SystemMemberMiniappCode extends Model{

    protected $pk = 'id';

    /**
     * 添加编辑
     * @param  array $param 数组
     */
    public static function edit(array $where,array $data){
        $miniapp = self::where($where)->find();
        if($miniapp){
            return self::where($where)->update($data);
        }else {
            return self::insert($data);
        }
    }
}
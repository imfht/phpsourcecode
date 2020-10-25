<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 配置
 */
namespace app\common\model;
use think\Model;

class SystemMemberCloudProduct extends Model{

    protected $pk = 'id';

    public function miniapp(){
        return $this->hasOne('app\common\model\SystemMiniapp','id','miniapp_id');
    }
}
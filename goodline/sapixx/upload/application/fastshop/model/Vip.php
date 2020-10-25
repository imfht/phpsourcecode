<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 判断是否VIP
 */
namespace app\fastshop\model;
use think\Model;

class Vip extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_vip';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    
}
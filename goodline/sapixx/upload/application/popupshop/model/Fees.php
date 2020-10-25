<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 运费管理
 */
namespace app\popupshop\model;
use think\Model;

class Fees extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_fees';
    protected $autoWriteTimestamp = true;
    
}
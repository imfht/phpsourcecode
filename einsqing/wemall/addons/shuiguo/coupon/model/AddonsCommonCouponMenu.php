<?php
namespace addons\common\coupon\model;

use think\Model;

class AddonsCommonCouponMenu extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    
}
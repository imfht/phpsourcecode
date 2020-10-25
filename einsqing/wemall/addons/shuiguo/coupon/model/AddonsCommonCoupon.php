<?php
namespace addons\common\coupon\model;

use think\Model;

class AddonsCommonCoupon extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected function getStatusAttr($value, $data)
    {
        $status = $data['status'];
        if ($status == 1) {
            $status = '已使用';
        }else{
            $status = '未使用';
        }
        return $status;
    }
    public function menu()
    {
        return $this->hasOne('AddonsCommonCouponMenu','id','coupon_menu_id');
    }
    public function user()
    {
        return $this->hasOne('app\common\model\User','id','user_id');
    }
    
}
<?php
namespace app\common\model;

use think\Model;

class ProductExchange extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
	public function type()
    {
        return $this->hasOne('OrderFeedbackType','id','reason_id');
    }
    public function order()
    {
        return $this->hasOne('Order','id','order_id');
    }
	public function product()
    {
        return $this->hasOne('Product','id','product_id');
    }
}
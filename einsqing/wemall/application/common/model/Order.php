<?php
namespace app\common\model;

use think\Model;

class Order extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $append  = ['payment'];

    protected $type = [
        'stores'      =>  'array',
        'coupon'      =>  'array',
        'totalprice'      =>  'float',
    ];

    protected function getPayStatusAttr($value, $data)
    {
        $pay_status = $data['pay_status'];
        if ($pay_status == 1) {
            $pay_status = '已支付';
        }else{
            $pay_status = '未支付';
        }
        return $pay_status;
    }
    protected function getStatusAttr($value, $data)
    {
        $status = $data['status'];
        if ($status == -3) {
            $status = '已退款';
        } elseif ($status == -2) {
            $status = '待退款';
        } elseif ($status == -1) {
            $status = '已取消';
        } elseif ($status == 0) {
            $status = '待发货';
        } elseif ($status == 1) {
            $status = '已发货';
        } elseif ($status == 2) {
            $status = '已完成';
        } elseif ($status == 3) {
            $status = '已评价';
        } else {
            $status = '未知状态';
        }
        return $status;
    }
    protected function getPaymentAttr($value, $data)
    {
        return model('Payment')->where('id',$data['payment_id'])->value('name');
    }

	public function user()
    {
        return $this->hasOne('User','id','user_id');
    }

    public function contact()
    {
        return $this->hasOne('OrderContact','order_id','id');
    }
    public function delivery()
    {
        return $this->hasOne('Delivery','id','delivery_id');
    }
    public function detail()
    {
        return $this->hasMany('OrderDetail');
    }
    public function fee()
    {
        return $this->hasMany('OrderFee');
    }
}
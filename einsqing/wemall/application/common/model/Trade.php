<?php
namespace app\common\model;

use think\Model;

class Trade extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
	public function user()
    {
        return $this->hasOne('User','id','user_id');
    }

}
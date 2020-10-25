<?php
namespace app\common\model;

use think\Model;

class UserContact extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
	// 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    public function country()
    {
        return $this->hasOne('Location','id','country_id');
    }    
    public function province()
    {
        return $this->hasOne('Location','id','province_id');
    }    
    public function city()
    {
        return $this->hasOne('Location','id','city_id');
    }
    public function district()
    {
        return $this->hasOne('Location','id','district_id');
    }
}
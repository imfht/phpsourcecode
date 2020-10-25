<?php
namespace app\common\model;

use think\Model;

class Location extends Model
{
	protected $resultSetType = 'collection';
    public function country()
    {
        return $this->hasOne('Location','id','pid');
    }
	public function province()
    {
        return $this->hasOne('Location','id','pid');
    }
    public function city()
    {
        return $this->hasOne('Location','id','pid');
    }

}
<?php
namespace app\common\model;

use think\Model;

class ProductCategory extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
	public function file()
    {
        return $this->hasOne('File','id','file_id');
    }
    // Spread修改器
    protected function getOpenAttr($value)
    {
        return true;
    }

}
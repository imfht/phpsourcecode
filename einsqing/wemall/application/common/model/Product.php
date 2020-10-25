<?php
namespace app\common\model;

use think\Model;

class Product extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $type = [
        'id'    =>  'integer',
        'price'     =>  'float',
        'old_price'     =>  'float',
        'score'    =>  'integer',
    ];
	public function category()
    {
        return $this->hasOne('product_category','id','category_id');
    }
    public function file()
    {
        return $this->hasOne('File','id','file_id');
    }
    public function skus()
    {
        return $this->hasMany('ProductSku');
    }
    public function getFilesAttr($value)
    {
        return $value ? model('File')->all($value)->toArray() : '';
    }
    public function getLabelsAttr($value)
    {
    	return $value ? model('ProductLabel')->all($value)->toArray() : '';
    }
    public function getSkuAttr($value)
    {
        $skulist = $value ? model('Sku')->all($value)->toArray() : '';
        return list_to_tree($skulist, 'id', 'pid', 'sub');
    }

 


}
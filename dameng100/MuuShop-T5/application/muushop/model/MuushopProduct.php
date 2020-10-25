<?php
namespace app\muushop\model;

use think\Model;

/**
 * 商品
 */
class MuushopProduct extends Model {
	
	/*
	protected $_validate = array(
		array('title', '1,64', '分类标题长度不对', 1, 'length'), //默认情况下用正则进行验证
		array('cat_id','0','请选择分类',1,'notequal'),
	);
	*/
	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	public function getListByPage($map,$order='create_time desc',$field='*',$r=20)
    {	
       
        $list=$this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
     
        return $list;
    }

    public function getList($map,$order='click_cnt desc',$limit=8,$field='*')
    {
        $lists = $this->where($map)->order($order)->limit($limit)->field($field)->select();
        return $lists;
    }

	/*
	 * 编辑商品
	 */
	public function editData($product)
	{
		if(empty($product['id'])){
			$res = $this->save($product);
		}else{
			$res = $this->save($product,['id'=>$product['id']]);
		}
		return $res;
	}

	/*
	 * 删除商品
	 */
	public function deleteData($ids)
	{
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$map['id'] = ['in',implode(',',$ids)];
		$res = $this->where($map)->delete();
		return true;
	}

	/*
	 * 获取商品信息
	 */
	public function getDataById($id)
	{	
		$map['id'] = $id;
		$res = $this->where($map)->find();
		if(!empty($res['sku_table'])) $res['sku_table'] = json_decode($res['sku_table'],true);
		return $res;
	}

	public function getDataByIds($ids)
	{
		!is_array($ids)&&$ids=explode(',',$ids);
		$map['id'] = ['in',$ids];
		$res = $this->where($map)->select();
		if(!empty($res['sku_table'])) $res['sku_table'] = json_decode($res['sku_table'],true);
		return $res;
	}

	/*
	 * 通过sku_id 获取商品
	 */
	public function getDataBySkuid($sku_id)
	{
		$sku_id = explode(';', $sku_id);
		$map['id'] = $sku_id[0];
		$res = $this->where($map)->find();
		if(!empty($res['sku_table'])) $res['sku_table'] = json_decode($res['sku_table'],true);
		return $res;
	}

	/**
     * 获取推荐位数据列表
     * @param $pos 推荐位ID 如：1-热卖，2-推荐，4-新品
     * @param null $cat_id
     * @param $limit
     * @param bool $field
     * @return mixed
     * @author 大蒙<59262424@qq.com>
     */
    public function position($pos, $cat_id = null, $limit = 5,$order='create_time desc,view desc'){

    	$map = [];
    	if($pos){
    		$map['position']= ['like','%'.$pos.'%'];
    	}
    	if($cat_id){
    		$map['cat_id'] = $cat_id;
    	}
        
        $res=$this->where($map)->order($order)->limit($limit)->select();
        foreach($res as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
        	$val['ori_price'] = price_convert('yuan',$val['ori_price']);
        }
        /* 读取数据 */
        return $res;
    }
}


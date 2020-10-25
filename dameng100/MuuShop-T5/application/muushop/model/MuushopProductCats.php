<?php
namespace app\muushop\model;

use think\Model;

/**
 * 商品分类
 */
class MuushopProductCats extends Model{
	
	/*
	protected $_validate = array(
		array('title','1,64','分类名称长度不符',1,'length'),
		array('title_en','0,128','分类英文标题长度不符',2,'length'),

	);
	*/
	/*
	 * 增加修改商品分类
	 */
	public function editData($data){
		if(empty($data['id'])){
			$ret = $this->allowField(true)->save($data);
		}else{
			$ret = $this->allowField(true)->save($data,['id'=>$data['id']]);
		}
		return $ret;
	}
	/*
	 * 删除商品分类
	 */
	public function deleteByIds($ids)
	{
		if(!is_array($ids)){
			$ids = array($ids);
		}
		$this->startTrans();
		$ret1 = $this->where('id in ('.implode(',',$ids).')')->delete();
		if($this->where('parent_id in ('.implode(',',$ids).')')->count()>0){
			$ret2 = $this->where('parent_id in  ('.implode(',',$ids).')')->save(array('parent_id'=>'0'));

		}else{
			$ret2=true;
		}
		$product_model = new MuushopProduct();
		if($product_model->where('cat_id in  ('.implode(',',$ids).')')->count()>0){

			$ret3 = $product_model->where('cat_id in  ('.implode(',',$ids).')')->save(array('cat_id'=>'0'));
		}else{
			$ret3 =true;
		}
		if($ret1 && $ret2 && $ret3){
			$this->commit();
			return true;
		}else{
			$this->rollback();
			return false;
		}
	}

	/**
	 * 获取分类信息
	 * @param  [type]  $map   [description]
	 * @param  integer $page  [description]
	 * @param  string  $order [description]
	 * @param  string  $field [description]
	 * @param  integer $r     [description]
	 * @return [type]         [description]
	 */
	public function getListByPage($map,$order='sort asc',$field='*',$r=20)
    {
        $list=$this->where($map)->order($order)->field($field)->paginate($r);
        
        return $list;
    }
    /**
     * 获取分类列表
     * @param  [type]  $map   [description]
     * @param  string  $order [description]
     * @param  integer $limit [description]
     * @param  string  $field [description]
     * @return [type]         [description]
     */
    public function getList($map,$order='sort asc',$field='*')
    {
        $list = $this->where($map)->order($order)->field($field)->select();
        $list = collection($list)->toArray();
        return $list;
    }

	/*
	 * 获取某个分类信息
	 */
	public function getDataById($id)
	{
		$res = $this->where('id = '.$id)->find();
		return $res;
	}

	/*
	 * 生成 可用于 config select 位置的 数组
	 */
	public function getListForConfig($show_titile='顶级分类')
	{
		$map['status'] = 1;
		$parent = $this->getList($map,$order='sort asc');
		$parent = model('common/Tree')->toFormatTree($parent,'title','id','parent_id');
		$all_cats =array_merge([0=>['id'=>0,'title_show'=>$show_titile]], $parent);
		foreach($all_cats as $cat){
			$select[$cat['id']] = html_entity_decode ($cat['title_show']);
		}
		return $select;
	}

	/*
	 * 生成 可用于 list select 位置的 数组 （主要是 列表页下来筛选）
	 */
	public function getListForSelect($show_titile='顶级分类')
	{
		$map['status'] = 1;
		$parent = $this->getList($map,$order='sort asc');
		$parent = model('common/Tree')->toFormatTree($parent,'title','id','parent_id');
		$all_cats =array_merge([0=>['id'=>0,'title_show'=>$show_titile]], $parent);
		foreach($all_cats as $cat)
		{
			$select[] = ['id'=>$cat['id'],'value'=>html_entity_decode ($cat['title_show'])];
		}
		return $select;
	}

	/*
	 * 获取在 这个父分类下所有的分类id
	 *
	 */
	public function getAllIdByPid($pid)
	{
		$ret= array($pid);
		is_array($pid) || $pid = array($pid);

		do{
			$map['parent_id'] = ['in',implode(',',$pid)];
			$ids = $this->where($map)->column('id');

			if($ids){
				$ret = array_merge($ret,$ids);
				$pid = $ids;
			}
		}
		while($ids);

		return $ret;
	}
	/**
	 * 排序完整URL数组
	 * 
	 * @return [type] [description]
	 */
	public function sort_url($sort){

		$sort_param = array(
			'all'=>'all',
			'sell_cnt'=>'sell_cnt_desc',
			'price'=>'price_desc',
			'comment_cnt'=>'comment_cnt_desc',
			'create_time'=>'create_time_desc'
		);
		
		switch ($sort)
		{
		case 'sell_cnt_desc':
		  $sort_param['sell_cnt']='sell_cnt_asc';
		  break;  
		case 'sell_cnt_asc':
		  $sort_param['sell_cnt']='sell_cnt_desc';
		  break;
		case 'price_desc':
		  $sort_param['price']='price_asc';
		  break;  
		case 'price_asc':
		  $sort_param['price']='price_desc';
		  break;
		case 'comment_cnt_desc':
		  $sort_param['comment_cnt']='comment_cnt_asc';
		  break;  
		case 'comment_cnt_asc':
		  $sort_param['comment_cnt']='comment_cnt_desc';
		  break;
		case 'create_time_desc':
		  $sort_param['create_time']='create_time_asc';
		  break;  
		case 'create_time_asc':
		  $sort_param['create_time']='create_time_desc';
		  break;
		}
		$sort_url = [];
		foreach($sort_param as $k=>$v){
			$sort_url[$k] = url('muushop/index/cats',['sort'=>$v]);
		}
		return $sort_url;
	}


}


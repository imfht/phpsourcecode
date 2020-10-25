<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Article extends Model
{

	function initialize()
	{
		parent::initialize();
	}

	//添加文章
	function add($params){
		$params['update_time'] = $params['create_time']; 
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			return $this->id;
		}else{
			return false;
		}
	}

	//修改文章
	function edit($params){
		$params['update_time'] = date('Y-m-d H:i:s');
		$result = $this->isUpdate(true)->allowField(true)->save($params);
		if($result){
			return true;
		}else{
			return false;
		}
	}
 
	//批量操作
	/**
	* 批量操作
	* $act  操作类型 delete
	* $params 参数
	*/
	function batches($act,$params){
		if($act == 'delete'){
			$result = $this->destroy($params);
		}elseif($act == 'move'){
			$ids = $params['ids'];
			$to_category_id = $params['to_category_id'];
			$result = $this->where('id','in',$ids)->update(['category_id'=>$to_category_id]);
		}
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	* 获取文章列表
	* $whith_page 0不分页  1分页返回对象  2分页返回数组
	* 
	*/
	function get_list($where = '',$order = 'id desc',$page_size=15,$whith_page=0){
		if(!$order){ $order = 'id desc';}
		if($whith_page == 0){
			$articles = $this->where($where)->order($order)->limit($page_size)->select();
			return $articles;
		}
		$articles = $this->where($where)->order($order)->paginate($page_size);
		if($whith_page == 1){
			return $articles;
		}
		$articles = $articles->toArray();
		foreach ($articles['data'] as $k => $v) {
			$articles['data'][$k]['category_name'] = cache('categorys')[$v['category_id']]['name'];
		}
		return $articles;
	}
	/**
	* 获取文章详情
	*/
	function get_details($id){
		$result = $this->get($id);
		$article = $result->toArray();
		$next = $this->where('id','<',$id)->where('category_id',$article['category_id'])->order('id desc')->find();
		$prev = $this->where('id','>',$id)->where('category_id',$article['category_id'])->order('id asc')->find();
		$prev = $prev?$prev->toArray():array('title'=>'返回列表','url'=>url('index/article/lists',['category_id'=>$article['category_id']]));
		$next = $next?$next->toArray():array('title'=>'返回列表','url'=>url('index/article/lists',['category_id'=>$article['category_id']]));
		$article['prev'] = array('title'=>$prev['title'],'url'=>$prev['url']);
		$article['next'] = array('title'=>$next['title'],'url'=>$next['url']);
		return $article;
	}
}
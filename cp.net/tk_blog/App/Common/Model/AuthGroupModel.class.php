<?php
namespace Common\Model;
use Think\Model;
/**
 * 权限规则model
 */
class AuthGroupModel extends Model{

	//删除数据
	public function execDelData($id){
		if (!$id) return false;
		$where = array('id'=>$id);
		return $this->where($where)->delete();
	}

	//查询一条数据
	public function getFindData($id){
		$where = array('id'=>$id);
		$data = $this->where($where)->find();
		return $data;
	}


	/**
	 * 查询所有数据并且显示分页
	 * @param int $limit 每页显示多少条数据 默认显示10条
	 * @param string|array $where 查询条件
	 * @param string  $order  排序条件
	 * @param Array  $setConfig  设置分页样式
	 * @return array
	 */
	public function getListData($limit=10,$where='',$order='',$setConfig = array()){
		if (empty($order)) {
			$order = "id ASC";
		}
		$count = $this->where($where)->count();   // 查询满足要求的总记录数
		$Page = new \Think\Page($count,$limit); // 实例化分页类 传入总记录数和每页显示的记录数
		//设置分页显示
		if (!$setConfig) {
			$Page->setConfig('prev','Prev');
			$Page->setConfig('next','Next');
		} else {
			$Page->setConfig('prev',$setConfig['prev']);
			$Page->setConfig('next',$setConfig['next']);
		}
		$show = $Page->show();
		$list = $this->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		//查出分类
		$result = array(
			'list' => $list,
			'page' => $show,
			'count'=> $count
		);
		return $result;
	}


}

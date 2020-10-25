<?php
namespace Common\Model;
use Think\Model;
/**
 * 权限规则model
 */
class AuthRuleModel extends Model{

	public function sendDeleteRule(){
		$id = I('post.id',0,'int');
		if ($this->where(array('id'=>$id))->delete()) {
			return true;
		} else {
			$this->error = "删除失败,请重试.^_^";
			return false;
		}
	}

	public function sendAddRule(){
		$id = I('post.id',0,'int');
		$name = trim(I('post.rule_name'));
		$title = trim(I('post.rule_title'));
		$status = I('post.status') ? '1' : '0';
		$rid = I('post.rid',0,'int');
		if (!$name || !$title) {
			$this->error = "名称或规则不能为空.^_^";
			return false;
		}
		$data = array(
			'name' => $name,
			'title' => $title,
			'status' => $status,
		);
		if ($id) {
			$data['pid'] = $id;
			//添加子权限
		} else {
			//添加父权限
			$data['pid'] = 0;
		}

		if ($rid) {
			//编辑
			$saveData = array(
				'name' => $name,
				'title' => $title,
				'status' => $status
			);
			$this->where(array('id'=>$rid))->save($saveData);
			return true;
		} else {
			if ($this->add($data)) {
				return true;
			} else {
				$this->error = "操作失败,请重试.^_^";
				return false;
			}
		}
	}



	/**
	 * 获取全部菜单
	 * @param  string $type tree获取树形结构 level获取层级结构
	 * @return array       	结构数据
	 */
	public function getTreeData($type='tree',$order=''){
		// 判断是否需要排序
		if(empty($order)){
			$data=$this->select();
		}else{
			$data=$this->order($order.' is null,'.$order)->select();
		}
		// 获取树形或者结构数据
		if($type=='tree'){
			$data=\Org\Cp\Data::tree($data,'title','id','pid');
		}elseif($type="level"){
			$data=\Org\Cp\Data::channelLevel($data,0,'&nbsp;','id');
			// 显示有权限的菜单
			$auth=new \Think\Auth();
			foreach ($data as $k => $v) {
				if ($auth->check($v['mca'],$_SESSION['user']['id'])) {
					foreach ($v['_data'] as $m => $n) {
						if(!$auth->check($n['mca'],$_SESSION['user']['id'])){
							unset($data[$k]['_data'][$m]);
						}
					}
				}else{
					// 删除无权限的菜单
					unset($data[$k]);
				}
			}
		}
		return $data;
	}
}

<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */
class ActionController extends \Common\Controller\AdminController {
	/**
	 * 行为日志列表
	 * @author huajie <banhuajie@163.com>
	 */
	public function actionLog() {
		//获取列表数据
		// $map['status'] = array('gt', -1);
		// $list = $this->lists('ActionLog', $map);
		// int_to_string($list);
		// foreach ($list as $key => $value) {
		// 	$model_id = get_document_field($value['model'], "name", "id");
		// 	$list[$key]['model_id'] = $model_id ? $model_id : 0;
		// }
		// $this->assign('_list', $list);
		// $this->meta_title = '行为日志';
		// $this->display();
		//获取列表数据
		$map['status']    =   array('gt', -1);
		//$list   =   $this->lists('ActionLog', $map);
		$model = D('ActionLog');
		$list = $model->order('id desc')->select();
		int_to_string($list);
		foreach ($list as $key=>$value){
			// $model_id                  =   get_document_field($value['model'],"name","id");
			// $list[$key]['model_id']    =   $model_id ? $model_id : 0;
			$value['title'] = get_action($value['action_id'],'title');
			$value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
			$value['user_id'] = get_nickname($value['user_id']);
			$data[] = $value;
		}

		$builder = new \OT\Builder();
		$builder->title('行为列表')
				->buttonNew(U('clear'),'清空',array('class'=>'am-btn-warning'))
				->buttonDelete(U('remove'),'删除',array('class'=>'am-btn-danger'))
				->keyText('id','编号')
				->keyText('title','行为名称')
				->keyText('user_id','执行者')
				->keyText('create_time','执行时间')
				->keyDoAction('edit?id=###','详细')
				->keyDoAction('remove?id=###','删除')
				->data($data)
				->pagination(D('Action')->count(),C('LIST_ROWS'))
				->display();
	}
	/**
	 * 查看行为日志
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit($id = 0) {
		// empty($id) && $this->error('参数错误！');
		
		// $info = M('ActionLog')->field(true)->find($id);
		
		// $this->assign('info', $info);
		// $this->meta_title = '查看行为日志';
		// $this->display();
		empty($id) && $this->error('参数错误！');
		$info = M('ActionLog')->field(true)->find($id);
		$info['title'] = get_action($info['action_id'],'title');
		$info['user_id'] = get_username($info['user_id']);
		$info['action_ip'] = long2ip($info['action_ip']);
		$info['create_time'] = date('Y-m-d H:i:s',$info['create_time']);
		$builder = new \OT\Builder('config');
		$builder->title('查看行为日志')
				->keyText('title','行为名称')
				->keyText('user_id','执行者')
				->keyText('action_ip','执行IP')
				->keyText('create_time','执行时间')
				->keyTextarea('remark','备注')
				->data($info)
				->buttonBack()
				->display();
	}
	/**
	 * 删除日志
	 * @param mixed $ids
	 * @author huajie <banhuajie@163.com>
	 */
	public function remove($ids = 0) {
		empty($ids) && $this->error('参数错误！');
		if (is_array($ids)) {
			$map['id'] = array('in', $ids);
		} 
		elseif (is_numeric($ids)) {
			$map['id'] = $ids;
		}
		$res = M('ActionLog')->where($map)->delete();
		if ($res !== false) {
			$this->success('删除成功！');
		} 
		else {
			$this->error('删除失败！');
		}
	}
	/**
	 * 清空日志
	 */
	public function clear() {
		$res = M('ActionLog')->where('1=1')->delete();
		if ($res !== false) {
			$this->success('日志清空成功！');
		} 
		else {
			$this->error('日志清空失败！');
		}
	}
}

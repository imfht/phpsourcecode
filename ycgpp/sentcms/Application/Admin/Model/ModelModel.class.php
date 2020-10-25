<?php

// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: colin <colin@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
class ModelModel extends Model {
	
	/* 自动验证规则 */
	protected $_validate = array(array('name', 'require', '标识不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT), array('name', '/^[a-zA-Z]\w{0,39}$/', '文档标识不合法', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH), array('name', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT), array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH), array('title', '1,30', '标题长度不能超过30个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH), array('list_grid', 'checkListGrid', '列表定义不能为空', self::MUST_VALIDATE, 'callback', self::MODEL_UPDATE),);
	
	/* 自动完成规则 */
	protected $_auto = array(array('name', 'strtolower', self::MODEL_INSERT, 'function'), array('create_time', NOW_TIME, self::MODEL_INSERT), array('update_time', NOW_TIME, self::MODEL_BOTH), array('status', '1', self::MODEL_INSERT, 'string'), array('field_sort', 'getFields', self::MODEL_BOTH, 'callback'), array('attribute_list', 'getAttribute', self::MODEL_BOTH, 'callback'),);
	
	/**
	 * 检查列表定义
	 * @param type $data
	 */
	protected function checkListGrid($data) {
		return I("post.extend") != 0 || !empty($data);
	}
	
	/**
	 * 处理字段排序数据
	 */
	protected function getFields($fields) {
		return empty($fields) ? '' : json_encode($fields);
	}
	
	protected function getAttribute($fields) {
		return empty($fields) ? '' : implode(',', $fields);
	}
	
	/**
	 * 更新一个或新增一个模型
	 * @return array
	 */
	public function update() {
		if(IS_POST){
			$data = $this->create();
			if($data){
				if (empty($data['id'])) {
					/*创建表*/
					$db = new \OT\Datatable();
					//文档模型
					if($data['extend'] == 1){
						//默认文档前缀
						$data['name'] = 'document_'.$data['name'];
					}
					$db->start_table($data['name'])->create_id(11 , '主键' , false)->create_key()->end_table($data['title'], $data['engine_type'])->create();
					$id = $this->add();
					// 清除模型缓存数据
					S('DOCUMENT_MODEL_LIST', null);
					
					//记录行为
					action_log('update_model', 'model', $id, UID);
					return $id ? array('info'=>'创建模型成功！','status'=>1) : array('info'=>'创建模型失败！','status'=>1);
				} 
				else {
					//修改
					$status = $this->save($data);
					// 清除模型缓存数据
					S('DOCUMENT_MODEL_LIST', null);
					//记录行为
					action_log('update_model','model',$data['id'],UID);
					return array('info'=>'保存模型成功！','status'=>1);
				}
			}else{
				return array('info'=>$this->getError(),'status'=>0);
			}
		}
	}

	public function del(){
		$id = I('id','','trim,intval');
		$model = $this->where(array('id'=>$id))->find();

		if ($model['extend'] == 0) {
			$this->error = "基础模型不允许删除！";
			return false;
		}elseif ($model['extend'] == 1){
			$tablename = 'document_'.$model['name'];
		}elseif ($model['extend'] == 2){
			$tablename = $model['name'];
		}
		//删除数据表
		$db = new \OT\Datatable();
		if ($db->CheckTable($tablename)) {
			//检测表是否存在
			$result = $db->del_table($tablename)->query();
			if (!$result) {
				return false;
				$this->error = "数据表删除失败！";
			}
		}
		$result = $this->where(array('id'=>$id))->delete();
		if ($result) {
			return ture;
		}else{
			$this->error = "模型删除失败！";
			return false;
		}
	}
}

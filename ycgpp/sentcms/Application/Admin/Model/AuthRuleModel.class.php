<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
/**
 * 权限规则模型
 * @author molong <molong@tensent.cn>
 */
class AuthRuleModel extends Model {
	
	const RULE_URL = 1;
	const RULE_MAIN = 2;
	
	protected $_validate = array(
		array('title', 'require', '节点名必须', Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH), 
		array('name', 'require', '节点标识必须', Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH), 
		array('group', 'require', '节点分组必须', Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH)
	);
	
	public function update() {
		$data = $this->create();
		if ($data['id']) {
			$result = $this->save();
		} 
		else {
			$result = $this->add();
		}
		return $result;
	}
}

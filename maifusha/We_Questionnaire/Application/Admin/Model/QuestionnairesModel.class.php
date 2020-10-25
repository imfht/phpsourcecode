<?php 
namespace Admin\Model;
use Think\Model\RelationModel;

class QuestionnairesModel extends RelationModel
{
	protected $_validate = array(
		array('type', 'require', '请选择问卷类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', 'require', '请输入问卷名字', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('description', 'require', '请输入问卷描述', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('expire_date', 'require', '请选择问卷过期时间', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	
	protected $_auto = array(
		array('create_date', 'date', self::MODEL_INSERT, 'function', array('Y-m-d')),
	);

	protected $_link = array(
		'Questions'	=>	self::HAS_MANY,
	);
	
}
?>
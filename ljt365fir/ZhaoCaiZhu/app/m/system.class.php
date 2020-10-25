<?php
/**
 * 系统表数据模型
 * @author 齐迹  email:smpss2012@gmail.com
 */
class m_system extends base_m {
	public function primarykey() {
		return 'sid';
	}
	public function tableName() {
		return base_Constant::TABLE_PREFIX . 'system';
	}
	public function relations() {
		return array ();
	}
}
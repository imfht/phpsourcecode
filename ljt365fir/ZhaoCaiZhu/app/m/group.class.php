<?php
/**
 * 管理员主表数据模型
 * @author 齐迹  email:smpss2012@gmail.com
 */
class m_group extends base_m {
	public function primarykey() {
		return 'gid';
	}
	public function tableName() {
		return base_Constant::TABLE_PREFIX . 'group';
	}
	public function relations() {
		return array ();
	}
}
<?php 

class AppBehavior extends Behavior {
	protected $options = array();
	
	public function run(&$params) {
		import('@.ORG.Scan');
		if (!file_exists(CONF_PATH.'install.lock') && MODULE_NAME != 'Install') {
			redirect(U('install/index'));
		} elseif(MODULE_NAME != 'Install') {
			if (!F('smtp')) {
				$value = M('Config')->where('name = "smtp"')->getField('value');
				F('smtp',unserialize($value));			
			}
			C('smtp', F('smtp'));
			if (!F('defaultinfo')) {
				$value = M('Config')->where('name = "defaultinfo"')->getField('value');
				F('defaultinfo',unserialize($value));			
			}
			C('defaultinfo', F('defaultinfo'));
			if (!F('sms') && $value = M('Config')->where('name = "sms"')->getField('value')) {
				F('sms',unserialize($value));
				C('sms', F('sms'));
			}
		}
	}
}
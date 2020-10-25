<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminLogsControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'log';
	 	$this->className = 'Logger';
	 	$this->lang = false;
		$this->noLink = true;

	 	$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_log' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'severity' => array('title' => $this->l('Severity (1-4)'), 'align' => 'center', 'width' => 50),
			'message' => array('title' => $this->l('Message')),
			'object_type' => array('title' => $this->l('Object type'), 'width' => 75),
			'object_id' => array('title' => $this->l('Object ID'), 'width' => 50),
			'error_code' => array('title' => $this->l('Error code'), 'width' => 75, 'prefix' => '0x'),
			'date_add' => array('title' => $this->l('Date'), 'width' => 150, 'align' => 'right', 'type' => 'datetime')
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Logs by e-mail'),
				'fields' =>	array(
					'PS_LOGS_BY_EMAIL' => array(
						'title' => $this->l('Minimum severity level'),
						'desc' => $this->l('Enter "5" if you do not want to receive any e-mails.').'<br />'.$this->l('E-mails will be sent to the shop owner.'),
						'cast' => 'intval',
						'type' => 'text',
						'size' => 5
					)
				),
				'submit' => array()
			)
		);

		parent::__construct();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

}

?>

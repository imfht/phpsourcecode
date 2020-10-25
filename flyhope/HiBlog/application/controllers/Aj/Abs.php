<?php
/**
 * AJAX抽象类
 *
 * @author chengxuan <i@chengxuan.li>
 */
abstract class Aj_AbsController extends AbsController {
	
	/**
	 * 是否检查包含AJAX的XmlHttpRequest头
	 * 
	 * @var int $_check_xml_http_request
	 */
	protected $_check_xml_http_request = true;
	
	/**
	 * 初始化方法
	 * {@inheritDoc}
	 * @see AbsController::init()
	 */
	public function init() {
		parent::init();
		
		//检查是否是AJAX请求
		if($this->_check_xml_http_request && !$this->getRequest()->isXmlHttpRequest()) {
			throw new \Exception\Program('Access Deny (Not ajax request).', 403);
		}
	}
	
}

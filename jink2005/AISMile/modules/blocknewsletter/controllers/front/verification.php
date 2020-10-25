<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5.0
 */
class BlocknewsletterVerificationModuleFrontController extends ModuleFrontController
{
	private $message = '';

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$this->message = $this->module->confirmEmail(Tools::getValue('token'));
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign('message', $this->message);
		$this->setTemplate('verification_execution.tpl');
	}
}

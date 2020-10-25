<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class Blockcontact extends Module
{
	public function __construct()
	{
		$this->name = 'blockcontact';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Block contact');
		$this->description = $this->l('Allows you to add extra information about customer service');
	}
	
	public function install()
	{
		return parent::install()
			&& Configuration::updateValue('blockcontact_telnumber', '')
			&& Configuration::updateValue('blockcontact_email', '')
			&& $this->registerHook('displayRightColumn')
			&& $this->registerHook('displayHeader');
	}
	
	public function uninstall()
	{
		// Delete configuration
		return Configuration::deleteByName('blockcontact_telnumber') && Configuration::deleteByName('blockcontact_email') && parent::uninstall();
	}
	
	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule'))
		{				
			Configuration::updateValue('blockcontact_telnumber', Tools::getValue('telnumber'));
			Configuration::updateValue('blockcontact_email', Tools::getValue('email'));
			$html .= '<div class="confirm">'.$this->l('Configuration updated').'</div>';
		}

		$html .= '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>			
				<label for="telnumber">'.$this->l('Telephone number:').'</label>
				<input type="text" id="telnumber" name="telnumber" value="'.((Configuration::get('blockcontact_telnumber') != '') ? Tools::safeOutput(Configuration::get('blockcontact_telnumber')) : '').'" />
				<div class="clear">&nbsp;</div>
				<label for="email">'.$this->l('Email:').'</label>
				<input type="text" id="email" name="email" value="'.((Configuration::get('blockcontact_email') != '') ? Tools::safeOutput(Configuration::get('blockcontact_email')) : '').'" />
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>';

		return $html;
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blockcontact.css', 'all');
	}
	
	public function hookDisplayRightColumn()
	{
		global $smarty;

		$smarty->assign(array(
			'telnumber' => Configuration::get('blockcontact_telnumber'),
			'email' => Configuration::get('blockcontact_email')
		));
		return $this->display(__FILE__, 'blockcontact.tpl');
	}
	
	public function hookDisplayLeftColumn()
	{
		return $this->hookDisplayRightColumn();
	}
}
?>

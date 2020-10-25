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
	
class blocksocial extends Module
{
	public function __construct()
	{
		$this->name = 'blocksocial';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('Block social');
		$this->description = $this->l('Allows you to add extra information about social networks');
	}
	
	public function install()
	{
		return (parent::install() AND Configuration::updateValue('blocksocial_weibo', '') && Configuration::updateValue('blocksocial_boke', '') && Configuration::updateValue('blocksocial_rss', '') && $this->registerHook('displayHeader') && $this->registerHook('displayFooter'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('blocksocial_weibo') AND Configuration::deleteByName('blocksocial_boke') AND Configuration::deleteByName('blocksocial_rss') AND parent::uninstall());
	}
	
	public function getContent()
	{
		// If we try to update the settings
		$output = '';
		if (isset($_POST['submitModule']))
		{	
			Configuration::updateValue('blocksocial_weibo', (($_POST['weibo'] != '') ? $_POST['weibo']: ''));
			Configuration::updateValue('blocksocial_boke', (($_POST['boke'] != '') ? $_POST['boke']: ''));		
			Configuration::updateValue('blocksocial_rss', (($_POST['rss_url'] != '') ? $_POST['rss_url']: ''));				
			$output = '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}
		
		return '
		<h2>'.$this->displayName.'</h2>
		'.$output.'
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="width2">				
				<label for="weibo">'.$this->l('鎴戠殑寰崥: ').'</label>
				<input type="text" id="weibo" name="weibo" value="'.Tools::safeOutput((Configuration::get('blocksocial_weibo') != "") ? Configuration::get('blocksocial_weibo') : "").'" />
				<div class="clear">&nbsp;</div>		
				<label for="boke">'.$this->l('鎴戠殑鍗氬: ').'</label>
				<input type="text" id="boke" name="boke" value="'.Tools::safeOutput((Configuration::get('blocksocial_boke') != "") ? Configuration::get('blocksocial_boke') : "").'" />
				<div class="clear">&nbsp;</div>		
				<label for="rss_url">'.$this->l('RSS URL: ').'</label>
				<input type="text" id="rss_url" name="rss_url" value="'.Tools::safeOutput((Configuration::get('blocksocial_rss') != "") ? Configuration::get('blocksocial_rss') : "").'" />
				<div class="clear">&nbsp;</div>						
				<br /><center><input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blocksocial.css', 'all');
	}
		
	public function hookDisplayFooter()
	{
		global $smarty;

		$smarty->assign(array(
			'weibo_url' => Configuration::get('blocksocial_weibo'),
			'boke_url' => Configuration::get('blocksocial_boke'),
			'rss_url' => Configuration::get('blocksocial_rss')
		));
		return $this->display(__FILE__, 'blocksocial.tpl');
	}
}
?>

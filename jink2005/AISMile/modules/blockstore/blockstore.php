<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_MB_VERSION_'))
	exit;

class BlockStore extends Module
{
	public function __construct()
	{
		$this->name = 'blockstore';
		$this->tab = 'front_office_features';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Stores block');
		$this->description = $this->l('Displays a block with a link to the store locator.');
	}

	public function install()
	{
		Configuration::updateValue('BLOCKSTORE_IMG', 'store.jpg');
		return parent::install() && $this->registerHook('rightColumn') && $this->registerHook('header');
	}

	public function uninstall()
	{
		Configuration::deleteByName('BLOCKSTORE_IMG');
		return parent::uninstall();
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		$this->smarty->assign('store_img', Configuration::get('BLOCKSTORE_IMG'));
		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'store s'
				.Shop::addSqlAssociation('store', 's');
		$total = Db::getInstance()->getValue($sql);

		if ($total > 0)
			return $this->display(__FILE__, 'blockstore.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blockstore.css', 'all');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			if (isset($_FILES['store_img']) && isset($_FILES['store_img']['tmp_name']) && !empty($_FILES['store_img']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['store_img'], 4000000))
					return $this->displayError($this->l('invalid image'));
				else
				{
					if (!move_uploaded_file($_FILES['store_img']['tmp_name'], dirname(__FILE__).'/'.$_FILES['store_img']['name']))
						return $this->displayError($this->l('an error occurred on uploading file'));
					else
					{
						if (Configuration::hasContext('BLOCKSTORE_IMG', null, Shop::getContext()) && Configuration::get('BLOCKSTORE_IMG') != $_FILES['store_img']['name'])
							@unlink(dirname(__FILE__).'/'.Configuration::get('BLOCKSTORE_IMG'));
						Configuration::updateValue('BLOCKSTORE_IMG', $_FILES['store_img']['name']);
						return $this->displayConfirmation($this->l('Settings are updated'));
					}
				}
			}
		}
		return '';
	}

	public function getContent()
	{
		$output = $this->postProcess().'
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>'.$this->l('Store block configuration').'</legend>';
		if (Configuration::get('BLOCKSTORE_IMG'))
			$output .= '<div class="margin-form"><img src="'.Tools::getProtocol().Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/'.Configuration::get('BLOCKSTORE_IMG').'" alt="'.$this->l('Store image').'" style="height:115px;margin-left: 100px;width:174px"/></div>';
		$output .= '
				<label for="store_img">'.$this->l('Change image').'</label>
				<div class="margin-form">
					<input id="store_img" type="file" name="store_img" /> ( '.$this->l('image will be displayed as 174x115').' )
				</div>

				<p class="center">
					<input class="button" type="submit" name="submitStoreConf" value="'.$this->l('Save').'"/>
				</p>
			</fieldset>
		</form>
		';
		return $output;
	}
}


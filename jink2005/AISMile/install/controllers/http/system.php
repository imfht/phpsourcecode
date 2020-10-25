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
 * Step 2 : check system configuration (permissions on folders, PHP version, etc.)
 */
class InstallControllerHttpSystem extends InstallControllerHttp
{
	public $tests = array();

	/**
	 * @var InstallModelSystem
	 */
	public $model_system;

	/**
	 * @see InstallAbstractModel::init()
	 */
	public function init()
	{
		require_once _PS_INSTALL_MODELS_PATH_.'system.php';
		$this->model_system = new InstallModelSystem();
	}

	/**
	 * @see InstallAbstractModel::processNextStep()
	 */
	public function processNextStep()
	{
	}

	/**
	 * Required tests must be passed to validate this step
	 *
	 * @see InstallAbstractModel::validate()
	 */
	public function validate()
	{
		$this->tests['required'] = $this->model_system->checkRequiredTests();

		return $this->tests['required']['success'];
	}

	/**
	 * Display system step
	 */
	public function display()
	{
		if (!isset($this->tests['required']))
			$this->tests['required'] = $this->model_system->checkRequiredTests();

		if (!isset($this->tests['optional']))
			$this->tests['optional'] = $this->model_system->checkOptionalTests();

		// Generate display array
		$this->tests_render = array(
			'required' => array(
				array(
					'title' => $this->l('PHP parameters:'),
					'checks' => array(
						'phpversion' => $this->l('Is PHP 5.1.2 or later installed ?'),
						'upload' => $this->l('Can upload files ?'),
						'system' => $this->l('Can create new files and folders ?'),
						'gd' => $this->l('Is GD Library installed ?'),
						'mysql_support' => $this->l('Is MySQL support is on ?'),
					)
				),
				array(
					'title' => $this->l('Recursive write permissions on files and folders:'),
					'checks' => array(
						'config_dir' => '~/config/',
						'cache_dir' => '~/cache/',
						'log_dir' => '~/log/',
						'img_dir' => '~/img/',
						'mails_dir' => '~/mails/',
						'module_dir' => '~/modules/',
						'theme_lang_dir' => '~/themes/default/lang/',
						'theme_pdf_lang_dir' => '~/themes/default/pdf/lang/',
						'theme_cache_dir' => '~/themes/default/cache/',
						'translations_dir' => '~/translations/',
						'customizable_products_dir' => '~/upload/',
						'virtual_products_dir' => '~/download/',
						'sitemap' => '~/sitemap.xml',
					)
				),
			),
			'optional' => array(
				array(
					'title' => $this->l('PHP parameters:'),
					'checks' => array(
						'fopen' => $this->l('Can open external URLs ?'),
						'register_globals' => $this->l('Is PHP register global option off (recommended) ?'),
						'gz' => $this->l('Is GZIP compression activated (recommended) ?'),
						'mcrypt' => $this->l('Is Mcrypt extension available (recommended) ?'),
						'magicquotes' => $this->l('Is PHP magic quotes option deactivated (recommended) ?'),
						'dom' => $this->l('Is Dom extension loaded ?'),
						'pdo_mysql' => $this->l('Is PDO MySQL extension loaded ?'),
					)
				),
			),
		);

		// If required tests failed, disable next button
		if (!$this->tests['required']['success'])
			$this->next_button = false;

		$this->displayTemplate('system');
	}
}


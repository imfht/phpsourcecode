<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class StatisticsControllerCore extends FrontController
{
	public $display_header = false;
	public $display_footer = false;

	protected $param_token;

	public function postProcess()
	{
		$this->param_token = Tools::getValue('token');
		if (!$this->param_token)
			die;

		if ($_POST['type'] == 'navinfo')
			$this->processNavigationStats();
		else if ($_POST['type'] == 'pagetime')
			$this->processPageTime();
		else
			exit;
	}

	/**
	 * Log statistics on navigation (resolution, plugins, etc.)
	 */
	protected function processNavigationStats()
	{
		$id_guest = (int)Tools::getValue('id_guest');
		if (sha1($id_guest._COOKIE_KEY_) != $this->param_token)
			die;

		$guest = new Guest($id_guest);
		$guest->javascript = true;
		$guest->screen_resolution_x = (int)Tools::getValue('screen_resolution_x');
		$guest->screen_resolution_y = (int)Tools::getValue('screen_resolution_y');
		$guest->screen_color = (int)Tools::getValue('screen_color');
		$guest->sun_java = (int)Tools::getValue('sun_java');
		$guest->adobe_flash = (int)Tools::getValue('adobe_flash');
		$guest->adobe_director = (int)Tools::getValue('adobe_director');
		$guest->apple_quicktime = (int)Tools::getValue('apple_quicktime');
		$guest->real_player = (int)Tools::getValue('real_player');
		$guest->windows_media = (int)Tools::getValue('windows_media');
		$guest->update();
	}

	/**
	 * Log statistics on time spend on pages
	 */
	protected function processPageTime()
	{
		$id_connection = (int)Tools::getValue('id_connections');
		$time = (int)Tools::getValue('time');
		$time_start = Tools::getValue('time_start');
		$id_page = (int)Tools::getValue('id_page');

		if (sha1($id_connection.$id_page.$time_start._COOKIE_KEY_) != $this->param_token)
			die;

		if ($time <= 0)
			die;

		Connection::setPageTime($id_connection, $id_page, substr($time_start, 0, 19), $time);
	}
}
<?php

/**
 * CacheController
 *
 * @package VGOT.CNÖ÷Ò³
 * @author pader
 * @copyright 2009
 * @version $Id$
 * @access public
 *
 *
 * $this->cache->get('cacheName');  Model: cache/reload_model/cacheName_return() to return
 *
 *
 *
**/
class CacheController extends Controller {

	function __construct()
	{
		parent::Controller();

		$this->load->library('cache');
	}

	function __destruct()
	{
		//printr($_COOKIE);
		//printr($this->db->queryRecords);
		printr('Memory Usage: '.round((memory_get_usage() / 1024),2).' KB');
	}

	/**
	 * CacheController::index()
	 *
	 * @return void
	**/
	function index()
	{
		$cache1 = $this->cache->get('test','cache/testModel/showTotalList');
		printr($cache1);
	}

	function session()
	{
		//setcookie('PHPSESSID','',time()-3600,'/');
		$this->load->library('session');

		$this->session->data('myfony',base64_encode($this->input->server('REQUEST_TIME')));

		echo anchor('test/cache/sessionpage2');

		//$this->session = NULL;
	}

	function sessionpage2()
	{
		$this->load->library('session');

		$d = $this->session->data('myfony');

		printr($d);

		echo anchor('test/cache/session');
	}

	public function config() {
		$testConfig = $this->config->get('test');
		printr($testConfig);
	}

	public function http() {
		$this->load->library('http');

		echo $this->http->get('http://127.0.0.1:8983/solr/bozhu/select?q=*%3A*&wt=json&indent=true');

		printr($this->http->getRequestDetail());
	}
	
	public function uuid() {
		$this->load->helper('misc');
		
		$this->load->database();
		
		$uuid = preg_replace_callback('/[xy]/', function ($matches) {
			return dechex('x' == $matches[0] ? mt_rand(0, 15) : (mt_rand(0, 15) & 0x3 | 0x8));
		} , 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');

		$uuid2 = preg_replace_callback('/[xy]/', create_function(
			'$matches',
			'return dechex("x" == $matches[0] ? mt_rand(0, 15) : (mt_rand(0, 15) & 0x3 | 0x8));'
		), 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');
		
		printr(get_uuid_from_mysql(), uuid(), $uuid, $uuid2, guid());
	}

}

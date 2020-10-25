<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/* Use this SQL to create database table

CREATE TABLE IF NOT EXISTS `vf_sessions` (
  `sid` char(32) NOT NULL default '0',
  `ip` char(15) NOT NULL default '0',
  `user_agent` char(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY `sid` (`sid`)
);

*/

/**
 * VgotFaster Session
 *
 * Use database to save data's session library
 *
 * @package VgotFaster
 * @subpackage Library
 * @author pader
 */
class Session {

	public $autoSave = TRUE;
	public $VF;
	public $sid;
	public $table;
	public $now;
	public $userData = array();
	public $config = array();

	/**
	 * 初始化会话
	 *
	 * 数据的存在性判断,创建或读取
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->VF =& getInstance();
		$this->VF->load->database();

		foreach (array('session_cookie_name','session_expire','session_use_database','session_time_to_update','session_match_ip','session_match_useragent') as $row) {
			$this->config[$row] = $this->VF->config->get('config',$row);
		}

		$this->table = $this->VF->config->get('config','session_db_table');
		$this->now = (int)$this->VF->input->server('REQUEST_TIME');
		$this->sid = $this->VF->input->cookie($this->config['session_cookie_name']);

		if (!$this->sid) {
			$this->sid = $this->createSession();
		} else {
			$sess = $this->getSession();
			if (empty($sess)) {
				$this->sid = $this->createSession();
			} else {
				$this->userData = unserialize($sess['data']);
			}
		}

		$this->garbageCollect();  //执行概率垃圾回收
	}

	/**
	 * Save|Get Session Data
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function data($data,$value=NULL)
	{
		if (is_array($data)) {
			foreach($data as $key => $val) {
				$this->userData[$key] = $val;
			}
		} elseif (is_null($value)) {
			return isset($this->userData[$data]) ? $this->userData[$data] : NULL;
		} else {
			$this->userData[$data] = $value;
		}
		$this->autoSave AND $this->saveData();
	}

	/**
	 * Delete Some Session Data
	 *
	 * @param string $keys
	 * @return void
	 */
	public function unsetData($keys)
	{
		if(is_array($keys)) {
			$this->userData = array_diff_key($this->userData,array_flip($keys));
		} elseif(isset($this->userData[$keys])) {
			unset($this->userData[$keys]);
		}

		$this->autoSave AND $this->saveData();
	}

	/**
	 * Save Session Data
	 *
	 * PHP结束时对当前数据的存储等处理
	 *
	 * @return void
	 */
	public function saveData()
	{
		$where = array('sid'=>$this->sid);
		$updateData = array(
			'last_activity' => $this->now,
			'data' => serialize($this->userData)
		);

		$this->VF->db->update($this->table,$updateData,$where);
	}

	/**
	 * Create Session
	 *
	 * @param string $sid A register sid
	 * @return string
	 */
	private function createSession($sid='')
	{
		$data = array(
			'sid' => empty($sid) ? $this->createSid() : $sid,
			'ip'  => $this->VF->input->ipAddress(),
			'user_agent'    => $this->VF->input->server('HTTP_USER_AGENT'),
			'last_activity' => $this->now,
			'data' => 'a:0:{}'  //array()
		);

		$this->VF->db->insert($this->table,$data);
		$this->sid = $data['sid'];
		$this->setcookie();

		return $data['sid'];
	}

	/**
	 * Get Session Data
	 *
	 * @return array
	 */
	private function getSession()
	{
		$where = array('sid' => $this->sid);

		//Match IP or UserAgent
		$this->config['session_match_ip'] AND $where['ip'] = $this->VF->input->ipAddress();
		$this->config['session_match_useragent'] AND $where['user_agent'] = substr($this->VF->input->server('HTTP_USER_AGENT'),0,50);

		$sess = $this->VF->db->get($this->table,'last_activity,data',$where)->row();

		//Keep activity
		if ($sess AND $this->now - $sess['last_activity'] > $this->config['session_time_to_update']) {
			$this->VF->db->update($this->table,array('last_activity'=>$this->now),array('sid'=>$this->sid));
			$this->setcookie();
		}

		return $sess;
	}

	private function setcookie()
	{
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		return setcookie(
			$this->config['session_cookie_name'],
			$this->sid,
			$this->now + $this->config['session_expire'],
			$this->VF->config->get('config','cookie_path'),
			$this->VF->config->get('config','cookie_domain')
		);
	}

	/**
	 * Destory Current Sesssion
	 *
	 * @return bool
	 */
	public function destory()
	{
		$this->VF->db->delete($this->table,array('sid'=>$this->sid));
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		setcookie(
			$this->config['session_cookie_name'], null, -$this->VF->config->get('config','session_expire'),
			$this->VF->config->get('config','cookie_path'), $this->VF->config->get('config','cookie_domain')
		);
	}

	/**
	 * Made A Unique Id
	 *
	 * @return string
	 */
	private function createSid()
	{
		$id = uniqid($this->VF->input->ipAddress(),TRUE);
		$id = join('',explode('.',$id));
		return md5($id);
	}

	/**
	 * Garbage Collect
	 *
	 * 使用一种随机计算方式产生一定的回收概率
	 * 按照此概率进行数据库过期数据回收
	 *
	 * @return void
	 */
	private function garbageCollect()
	{
		srand($this->now);
		if((rand() % 100) < 5) {  //回收概率
			$expire = $this->now - $this->config['session_expire'];
			$this->VF->db->delete($this->table,"`last_activity` < $expire");
			_systemLog('Session garbage collection performed.');
		}
	}

}

//.
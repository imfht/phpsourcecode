<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/05 0011
 * Time: 下午 4:59
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 权限验证模型
 * ==========================================
 */

/**
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *   $this->auth_model->auth_check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *   $this->auth_model->auth_check('规则1,规则2','用户id','and')
 *   第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(pk_auth_group_access表 定义了用户所属用户组)。
 *   我们需要设置每个用户组拥有哪些规则(pk_auth_group 定义了用户组权限)
 * 4，支持规则表达式。
 *   在pk_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。
 */

/**
 * 数据库设计
 * -- ----------------------------
 * -- pk_auth_rule，操作规则表，
 * -- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，
 * -- condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
 * -- ----------------------------
 * DROP TABLE IF EXISTS `pk_auth_rule`;
 * CREATE TABLE `pk_auth_rule` (
 * `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
 * `pid` int(11) NOT NULL COMMENT '父id',
 * `name` char(80) NOT NULL DEFAULT '' COMMENT '操作规则唯一标识（控制器/方法）',
 * `title` char(20) NOT NULL DEFAULT '' COMMENT '操作规则中文名称',
 * `icon` varchar(255) NOT NULL COMMENT '操作规则图标（仅是父类有效）',
 * `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型',
 * `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1正常，0禁用',
 * `condition` char(100) NOT NULL DEFAULT '' COMMENT '操作规则表达式',
 * `islink` tinyint(1) NOT NULL DEFAULT '1' COMMENT '连接：1是，0不是',
 * `sort` int(11) NOT NULL COMMENT '排序',
 * `tips` text NOT NULL COMMENT '提示描述',
 * PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT '操作规则表';
 * -- ----------------------------
 * -- pk_auth_group 用户组表，
 * -- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
 * -- ----------------------------
 * CREATE TABLE `pk_auth_group` (
 * `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '组(表)id',
 * `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
 * `rules` varchar(512) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则","隔开',
 * `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1正常，0禁用',
 * PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT '用户组表';
 * -- ----------------------------
 * -- pk_auth_group_access 用户权限组关联明细表
 * -- admin_id:用户id，group_id：用户组id
 * -- ----------------------------
 * CREATE TABLE `pk_auth_group_access` (
 * `admin_id` mediumint(8) unsigned NOT NULL COMMENT '管理员用户id',
 * `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
 * UNIQUE KEY `admin_id_group_id` (`admin_id`,`group_id`),
 * KEY `admin_id` (`admin_id`),
 * KEY `group_id` (`group_id`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '用户权限组关联明细表';
 *
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

	public $auth_config;

	public function __construct() {
		parent::__construct();
		$this -> auth_config = $this -> config -> item('auth');
	}

	/**
	 * 函数：检查权限
	 * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
	 * @param $admin_id  int     认证用户的id
	 * @param string mode        执行check的模式
	 * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
	 * @return boolean           通过验证返回true;失败返回false
	 */
	public function auth_chack($name, $admin_id, $type = 1, $mode = 'url', $relation = 'or') {
		if (!$this -> auth_config['AUTH_ON']) {
			return true;
		}
		//获取用户需要验证的所有有效规则列表
		$authList = $this -> get_auth_list($admin_id, $type);
		if (is_string($name)) {
			$name = strtolower($name);
			if (strpos($name, ',') !== false) {
				$name = explode(',', $name);
			} else {
				$name = array($name);
			}
		}
		//保存验证通过的规则名
		$list = array();
		if ($mode == 'url') {
			$REQUEST = unserialize(strtolower(serialize($_REQUEST)));
		}
		foreach ($authList as $auth) {
			$query = preg_replace('/^.+\?/U', '', $auth);
			if ($mode == 'url' && $query != $auth) {
				parse_str($query, $param);
				//解析规则中的param
				$intersect = array_intersect_assoc($REQUEST, $param);
				$auth = preg_replace('/\?.*$/U', '', $auth);
				//如果节点相符且url参数满足
				if (in_array($auth, $name) && $intersect == $param) {
					$list[] = $auth;
				}
			} else if (in_array($auth, $name)) {
				$list[] = $auth;
			}
		}
		if ($relation == 'or' and !empty($list)) {
			return true;
		}
		$diff = array_diff($name, $list);
		if ($relation == 'and' and empty($diff)) {
			return true;
		}
		return false;
	}

	/**
	 * 函数：获得权限列表
	 * @param integer $uid  用户id
	 * @param integer $type
	 * @return array
	 */
	public function get_auth_list($admin_id, $type) {
		static $_authList = array();
		//保存用户验证通过的权限列表
		$t = implode(',', (array)$type);
		if (isset($_authList[$admin_id . $t])) {
			return $_authList[$admin_id . $t];
		}
		if ($this -> auth_config['AUTH_TYPE'] == 2 && isset($_SESSION['_AUTH_LIST_' . $admin_id . $t])) {
			return $_SESSION['_AUTH_LIST_' . $admin_id . $t];
		}
		//读取用户所属用户组
		$groups = $this -> get_auth_group($admin_id);
		$ids = array();
		//保存用户所属用户组设置的所有权限规则id
		foreach ($groups as $g) {
			$ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
		}
		$ids = array_unique($ids);
		if (empty($ids)) {
			$_authList[$admin_id . $t] = array();
			return array();
		}
		$map = array('type' => $type, 'status' => 1, );
		//读取用户组所有权限规则
		$rules = $this -> db -> select('condition,name') -> where($map) -> where_in('id', $ids) -> get('auth_rule') -> result_array();
		//循环规则，判断结果。
		$authList = array();
		foreach ($rules as $rule) {
			if (!empty($rule['condition'])) {//根据condition进行验证
				$user = $this -> get_admin_info_by_id($admin_id);
				//获取用户信息,一维数组
				$command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
				@(eval('$condition=(' . $command . ');'));
				if ($condition) {
					$authList[] = strtolower($rule['name']);
				}
			} else {
				//只要存在就记录
				$authList[] = strtolower($rule['name']);
			}
		}
		$_authList[$admin_id . $t] = $authList;
		if ($this -> auth_config['AUTH_TYPE'] == 2) {
			//规则列表结果保存到session
			$_SESSION['_AUTH_LIST_' . $uid . $t] = $authList;
		}
		return array_unique($authList);
	}

	/**
	 * 函数：获取用户组信息
	 * @param  int $admin_id  用户id
	 * @return array 用户所属的用户组 array
	 */
	public function get_auth_group($admin_id) {
		$prefix = $this -> db -> dbprefix;
		$sql = "SELECT  `admin_id` ,  `group_id` ,  `title` ,  `rules` 
				FROM {$prefix}auth_group_access a
				INNER JOIN {$prefix}auth_group g ON a.group_id = g.id
				WHERE (
				a.admin_id =  '{$admin_id}'
				AND g.status =  '1'
				)";
		return $this -> db -> query($sql) -> result_array();
	}

	/**
	 * 函数：通过id获取管理员用户信息
	 * @param int $admin_id 管理员用户id
	 */
	public function get_admin_info_by_id($admin_id) {
		$condition = array('admin_id' => $admin_id);
		return $this -> db -> where($condition) -> get('admin') -> row_array();
	}

}

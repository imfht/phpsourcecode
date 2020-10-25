<?php

/**
 * 安装
 * 
 * @author ShuangYa
 * @package Blog
 * @category Install
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

class Install {
	public static function actionInstall() {
		header('Content-type: text/html; charset=utf-8');
		include('install.phtml');
	}
	public static function actionEnvcheck() {
		header('Content-type: application/json; charset=utf-8');
		$result = [
			['name' => 'XMLWriter', 'support' => class_exists('XMLWriter', FALSE)],
			['name' => 'PDO', 'support' => class_exists('PDO', FALSE)]
		];
		echo json_encode($result);
	}
	public static function actionTestdb() {
		header('Content-type: text/html; charset=utf-8');
		$dsn = 'mysql:host=' . $_POST['dbhost'] . ';port=' . $_POST['dbport'] . ';charset=utf8';
		try {
			$link = new PDO($dsn, $_POST['dbuser'], $_POST['dbpwd']);
		}
		catch (PDOException $e) {
			echo '连接数据库失败';
			exit;
		}
		if ($link === FALSE) {
			echo '连接数据库失败';
			exit;
		}
		echo 'success';
	}
	public static function actionProcessing() {
		header('Content-type: text/html; charset=utf-8');
		$dsn = 'mysql:host=' . $_POST['dbhost'] . ';port=' . $_POST['dbport'] . ';charset=utf8';
		try {
			$link = new PDO($dsn, $_POST['dbuser'], $_POST['dbpwd']);
		} catch (PDOException $e) {
			echo '连接数据库失败';
			exit;
		}
		$result = 'SELECT count(*) as num FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?';
		$st = $link->prepare($result);
		$st->bindValue(1, $_POST['dbname']);
		$st->execute();
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$rs = $st->fetch();
		if (intval($rs['num']) === 0) {
			//创建数据库
			if ($link->query('CREATE DATABASE `' . $_POST['dbname'] . '`') === FALSE) {
				echo '创建数据库 ', $_POST['dbname'], '失败';
				exit;
			}
		}
		$link->query('use `' . $_POST['dbname'] . '`');
		//建立数据表
		$sqls = explode(';', file_get_contents('data/mysql.sql'));
		foreach ($sqls as $sql) {
			$sql = str_replace('#@__', $_POST['dbprefix'], trim($sql));
			if (empty($sql)) {
				continue;
			}
			if ($link->query($sql) === FALSE) {
				echo '执行语句失败：', $sql;
				exit;
			}
		}
		//建立管理员密码
		$config = require('data/config.php');
		$password = hash_hmac('md5', $_POST['password'], $config['securityKey']);
		$sql = 'INSERT INTO `' . $_POST['dbprefix'] . 'option` VALUES (\'password\',\'' . $password . '\')';
		if ($link->query($sql) === FALSE) {
			echo '执行语句失败：', $sql;
			exit;
		}
		//写入配置文件
		$config = file_get_contents('data/config.php');
		$replace = [ 'cookie', 'dbhost', 'dbport', 'dbuser', 'dbpwd', 'dbname', 'dbprefix' ];
		foreach ($replace as $k) {
			$config = str_replace('##' . $k . '##', $_POST[$k], $config);
		}
		if (@file_put_contents(APP_PATH . 'config.php', $config) === FALSE) {
			echo '写入配置文件失败！请确认 ', APP_PATH, 'config.php', ' 可写';
			exit;
		}
		file_put_contents('install.lock', 'ok');
		echo 'success';
	}
}
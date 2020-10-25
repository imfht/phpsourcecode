<?php

/**
 * 文章类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\model;
use \Sy;
use \IXR_Client;
use \sy\lib\db\Mysql;
use \sy\base\Router;
use \blog\libs\Common;
use \blog\libs\template\ListArticle;
use \blog\libs\template\ItemArticle;
use \blog\model\Meta;

class Article {
	public function __construct() {
	}
	/**
	 * 获取一篇文章
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public static function get($id) {
		return new ItemArticle(Mysql::i()->getOne("SELECT * FROM `#@__article` WHERE id = ?", [$id]));
	}
	/**
	 * 修改一篇文章
	 * @access public
	 * @param int $id
	 * @param string $title 标题
	 * @param string $tags
	 * @param int $time
	 * @param string $body
	 */
	public static function set($id, $title, $tags, $time, $body = NULL) {
		//处理Tag
		$new_tag = explode(',', $tags);
		$article = self::get($id);
		$old_tag = explode(',', $article->tags);
		//取差
		$add = array_diff($new_tag, $old_tag);
		$remove = array_diff($old_tag, $new_tag);
		foreach ($add as $t) {
			if (empty($t)) {
				continue;
			}
			//检查此Tag是否存在
			$tag_me = Meta::getByName($t);
			if (!empty($tag_me['id'])) {
				$mid = $tag_me['id'];
			} else {
				$mid = Meta::add($t);
			}
			Mysql::i()->query("UPDATE `#@__meta` SET num = num+1 WHERE id = ?", [$mid]);
			$sql = "INSERT INTO `#@__relation`(`aid`,`mid`) VALUES ('$id','$mid')";
			Mysql::i()->query($sql);
		}
		foreach ($remove as $t) {
			if (empty($t)) {
				continue;
			}
			$tag_me = Meta::getByName($t);
			$mid = $tag_me['id'];
			Mysql::i()->query("UPDATE `#@__meta` SET num = num-1 WHERE id = ?", [$mid]);
			$sql = "DELETE FROM `#@__relation` WHERE aid = ? AND mid = ?";
			Mysql::i()->query($sql, [$id, $mid]);
		}
		//更新文章内容
		$title = addslashes($title);
		$tags = addslashes($tags);
		$sql = "UPDATE `#@__article` SET title = '$title', tags = '$tags', modify = '$time'";
		if ($body !== NULL) {
			$sql .= ", body = '" . addslashes($body) . "'";
		}
		$sql .= ' WHERE id = ?';
		Mysql::i()->query($sql, [$id]);
	}
	/**
	 * 增加一篇文章
	 * @access public
	 * @param string $title 标题
	 * @param string $tags
	 * @param int $time
	 * @param string $body
	 * @return int
	 */
	public static function add($title, $tags, $time, $body) {
		//插入文章
		$title = addslashes($title);
		$itags = addslashes($tags);
		$body = addslashes($body);
		$sql = "INSERT INTO `#@__article`(`title`,`tags`,`publish`,`modify`,`body`) VALUES ('$title','$itags','$time','$time','$body')";
		Mysql::i()->query($sql);
		$id = Mysql::i()->getLastId();
		//处理Tag
		$tag = explode(',', $tags);
		foreach ($tag as $t) {
			if (empty($t)) {
				continue;
			}
			//检查此Tag是否存在
			$tag_me = Meta::getByName($t);
			if (!empty($tag_me['id'])) {
				$mid = $tag_me['id'];
			} else {
				$mid = Meta::add($t);
			}
			Mysql::i()->query("UPDATE `#@__meta` SET num = num+1 WHERE id = ?", [$mid]);
			$sql = "INSERT INTO `#@__relation`(`aid`,`mid`) VALUES ('$id','$mid')";
			Mysql::i()->query($sql);
		}
		self::sendPing($id);
		return $id;
	}
	/**
	 * 删除一篇文章
	 * @access public
	 * @param int $id
	 */
	public static function del($id) {
		$article = self::get($id);
		$title = $article->title;
		if (empty($title)) {
			return;
		}
		$tags = explode(',', $article->tags);
		foreach ($tags as $t) {
			Mysql::i()->query("UPDATE `#@__meta` SET num = num-1 WHERE title = ?", [$t]);
		}
		Mysql::i()->query('DELETE FROM `#@__relation` WHERE aid = ?', [$id]);
		Mysql::i()->query('DELETE FROM `#@__article` WHERE id = ?', [$id]);
	}
	/**
	 * 获取列表
	 * @access public
	 * @param array $param
	 * @param mixed $param[body] 是否返回文章内容
	 * @param mixed $param[find] 筛选方式
	 * @param string $param[id/tag] 筛选条件
	 * @param string $param[limit] 数量，SQL形式(0,30)
	 * @return Object(ListArticle)
	 */
	public static function getList($param = []) {
		extract($param, EXTR_SKIP);
		if ($body) {
			if (isset($find)) {
				$row = 'a.*';
			} else {
				$row = '*';
			}
		} else {
			if (isset($find)) {
				$row = 'a.id,a.title,a.publish,a.modify,a.tags';
			} else {
				$row = 'id,title,publish,modify,tags';
			}
		}
		if ($find === 'id') {
			$sql = 'SELECT b.mid,' . $row . ' FROM `#@__article` a,`#@__relation` b WHERE a.id = b.aid AND b.mid = ? ORDER BY a.publish DESC ';
			$data = [$id];
		} elseif ($find === 'tag') {
			$sql = 'SELECT b.mid,' . $row . ' FROM `#@__article` a,`#@__relation` b,`#@__meta` c WHERE a.id = b.aid AND b.mid = c.id AND c.title = ? ORDER BY a.publish DESC ';
			$data = [$tag];
		} else {
			$sql = 'SELECT ' . $row . ' FROM `#@__article` ORDER BY publish DESC ';
			$data = [];
		}
		if (isset($limit)) {
			$sql .= 'LIMIT ' . $limit;
		}
		$r = Mysql::i()->query($sql, $data);
		$num = self::getNum($param);
		return new ListArticle($r, $num, $param);
	}
	/**
	 * 获取满足条件的文章数量
	 * @access public
	 * @param array $param
	 * @param mixed $param[find] 筛选方式
	 * @param string $param[id/tag] 筛选条件
	 * @return int
	 */
	public static function getNum($param = []) {
		if ($param['find'] === 'id') {
			$sql = 'SELECT count(*) as num FROM `#@__article` a,`#@__relation` b WHERE a.id = b.aid AND b.mid = ?';
			$data = [$param['id']];
		} elseif ($param['find'] === 'tag') {
			$sql = 'SELECT count(*) as num FROM `#@__article` a,`#@__relation` b,`#@__meta` c WHERE a.id = b.aid AND b.mid = c.id AND c.title = ?';
			$data = [$param['tag']];
		} else {
			$sql = 'SELECT count(*) as num FROM `#@__article`';
			$data = [];
		}
		$r = Mysql::i()->getOne($sql, $data);
		return $r['num'];
	}
	/**
	 * 发送Ping通知和百度主动推送
	 * @access public
	 * @param int $id 文章ID
	 */
	public static function sendPing($id) {
		$baseUri = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		if (isset($_SERVER['SERVER_PORT'])) {
			if ((isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] != '443') || (!isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] != '80')) {
				$baseUri .= ':' . $_SERVER['SERVER_PORT'];
			}
		}
		if (!class_exists('IXR_Client', FALSE)) {
			require(Sy::$appDir . 'third_party/IXR_Library.php');
		}
		$servers = explode("\n", Common::option('seoPing'));
		foreach ($servers as $server) {
			$client = new IXR_Client($server);
			$client->timeout = 3; //较短的超时时间
			$client->useragent .= ' -- SYBlog/' . Common::VERSION;
			$client->query('weblogUpdates.extendedPing', Common::option('sitename'), $baseUri . Router::createUrl(), $baseUri . Router::createUrl(['index/article/view'. 'id' => $id]), $baseUri . Router::createUrl('index/feed/rss', 'xml'));
		}
		//百度主动推送
		$baiduSubmit = unserialize(Common::option('seoBaiduSubmit'));
		if ($baiduSubmit['enable']) {
			Common::curlGet('http://data.zz.baidu.com/urls?' . http_build_query(['site' => $baiduSubmit['site'], 'token' => $baiduSubmit['token']]), ['header' => ['Content-Type: text/plain'], 'post' => $baseUri . Router::createUrl(['index/article/view'. 'id' => $id])]);
		}
	}
}
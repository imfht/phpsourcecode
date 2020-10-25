<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library;

use tfc\mvc\Mvc;
use posts\services\DataPosts;

/**
 * UrlHelper class file
 * Url辅助类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: UrlHelper.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library
 * @since 1.0
 */
class UrlHelper
{
	/**
	 * @var instance of library\UrlHelper
	 */
	protected static $_instance = null;

	/**
	 * 构造方法：禁止被实例化
	 */
	protected function __construct()
	{
	}

	/**
	 * 魔术方法：禁止被克隆
	 */
	private function __clone()
	{
	}

	/**
	 * 单例模式：获取本类的实例
	 * @return \library\UrlHelper
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * 获取文档列表链接
	 * @param array $data
	 * @return string
	 */
	public function getPostIndex(array $data)
	{
		$catId = isset($data['category_id']) ? (int) $data['category_id'] : 0;
		if ($catId <= 0) {
			return '';
		}

		$urlManager = Mvc::getView()->getUrlManager();
		return $urlManager->getUrl('index', 'show', 'posts', array('catid' => $catId));
	}

	/**
	 * 获取文档链接
	 * @param array $data
	 * @return string
	 */
	public function getPostView(array $data)
	{
		$isJump = isset($data['is_jump']) ? $data['is_jump'] : '';
		$jumpUrl = isset($data['jump_url']) ? $data['jump_url'] : '';
		if ($isJump === DataPosts::IS_JUMP_Y) {
			return $jumpUrl;
		}

		$postId = isset($data['post_id']) ? (int) $data['post_id'] : 0;
		if ($postId <= 0) {
			return '';
		}

		$urlManager = Mvc::getView()->getUrlManager();
		return $urlManager->getUrl('view', 'show', 'posts', array('id' => $postId));
	}

	/**
	 * 获取专题链接
	 * @param array $data
	 * @return string
	 */
	public function getTopicView(array $data)
	{
		$topicKey = isset($data['topic_key']) ? trim($data['topic_key']) : '';
		if ($topicKey === '') {
			return '';
		}

		$urlManager = Mvc::getView()->getUrlManager();
		return $urlManager->getUrl('view', 'show', 'topic', array('key' => $topicKey));
	}
}

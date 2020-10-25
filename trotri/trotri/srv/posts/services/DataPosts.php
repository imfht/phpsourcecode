<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace posts\services;

use posts\library\Lang;

/**
 * DataPosts class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataPosts.php 1 2014-10-17 11:27:20Z Code Generator $
 * @package posts.services
 * @since 1.0
 */
class DataPosts
{
	/**
	 * @var string 排序字段：sort
	 */
	const ORDER_BY_SORT = 'sort';

	/**
	 * @var string 排序字段：点击次数
	 */
	const ORDER_BY_HITS = 'hits DESC';

	/**
	 * @var string 排序字段：赞美次数
	 */
	const ORDER_BY_PRAISE = 'praise_count DESC';

	/**
	 * @var string 排序字段：评论次数
	 */
	const ORDER_BY_COMMENT = 'comment_count DESC';

	/**
	 * @var string 是否头条：y
	 */
	const IS_HEAD_Y = 'y';

	/**
	 * @var string 是否头条：n
	 */
	const IS_HEAD_N = 'n';

	/**
	 * @var string 是否推荐：y
	 */
	const IS_RECOMMEND_Y = 'y';

	/**
	 * @var string 是否推荐：n
	 */
	const IS_RECOMMEND_N = 'n';

	/**
	 * @var string 是否跳转：y
	 */
	const IS_JUMP_Y = 'y';

	/**
	 * @var string 是否跳转：n
	 */
	const IS_JUMP_N = 'n';

	/**
	 * @var string 是否发表：y
	 */
	const IS_PUBLISHED_Y = 'y';

	/**
	 * @var string 是否发表：n
	 */
	const IS_PUBLISHED_N = 'n';

	/**
	 * @var string 评论设置：publish
	 */
	const COMMENT_STATUS_PUBLISH = 'publish';

	/**
	 * @var string 评论设置：draft
	 */
	const COMMENT_STATUS_DRAFT = 'draft';

	/**
	 * @var string 评论设置：forbidden
	 */
	const COMMENT_STATUS_FORBIDDEN = 'forbidden';

	/**
	 * @var string 允许其他人编辑：y
	 */
	const ALLOW_OTHER_MODIFY_Y = 'y';

	/**
	 * @var string 允许其他人编辑：n
	 */
	const ALLOW_OTHER_MODIFY_N = 'n';

	/**
	 * @var string 是否删除：y
	 */
	const TRASH_Y = 'y';

	/**
	 * @var string 是否删除：n
	 */
	const TRASH_N = 'n';

	/**
	 * 获取“是否头条”所有选项
	 * @return array
	 */
	public static function getIsHeadEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_HEAD_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_HEAD_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否推荐”所有选项
	 * @return array
	 */
	public static function getIsRecommendEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_RECOMMEND_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_RECOMMEND_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否跳转”所有选项
	 * @return array
	 */
	public static function getIsJumpEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_JUMP_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_JUMP_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否发表”所有选项
	 * @return array
	 */
	public static function getIsPublishedEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::IS_PUBLISHED_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::IS_PUBLISHED_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“评论设置”所有选项
	 * @return array
	 */
	public static function getCommentStatusEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::COMMENT_STATUS_PUBLISH => Lang::_('SRV_ENUM_POSTS_COMMENT_STATUS_PUBLISH'),
				self::COMMENT_STATUS_DRAFT => Lang::_('SRV_ENUM_POSTS_COMMENT_STATUS_DRAFT'),
				self::COMMENT_STATUS_FORBIDDEN => Lang::_('SRV_ENUM_POSTS_COMMENT_STATUS_FORBIDDEN'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“允许其他人编辑”所有选项
	 * @return array
	 */
	public static function getAllowOtherModifyEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::ALLOW_OTHER_MODIFY_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::ALLOW_OTHER_MODIFY_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“是否删除”所有选项
	 * @return array
	 */
	public static function getTrashEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::TRASH_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::TRASH_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

}

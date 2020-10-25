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

use tfc\ap\Ap;

/**
 * SubmitType class file
 * 表单提交后跳转方式管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: SubmitType.php 1 2013-04-05 01:08:06Z huan.song $
 * @package library
 * @since 1.0
 */
class SubmitType
{
	/**
	 * @var string 表单提交后跳转方式：保存并跳转到编辑页
	 */
	const TYPE_SAVE = 'save';

	/**
	 * @var string 表单提交后跳转方式：保存并跳转到列表页
	 */
	const TYPE_SAVE_CLOSE = 'save_close';

	/**
	 * @var string 表单提交后跳转方式：保存并跳转到新增页
	 */
	const TYPE_SAVE_NEW = 'save_new';

	/**
	 * @var string 表单提交后默认的跳转方式
	 */
	const TYPE_DEFAULT = self::TYPE_SAVE;

	/**
	 * @var array 寄存表单提交后跳转方式
	 */
	public static $types = array(
		self::TYPE_SAVE,
		self::TYPE_SAVE_CLOSE,
		self::TYPE_SAVE_NEW
	);

	/**
	 * 获取当前表单提交方式
	 * @return string
	 */
	public static function getType()
	{
		static $submitType = null;

		if ($submitType === null) {
			$submitType = Ap::getRequest()->getTrim('submit_type');
			if (!in_array($submitType, self::$types)) {
				$submitType = self::TYPE_DEFAULT;
			}
		}

		return $submitType;
	}

	/**
	 * 返回表单提交后跳转方式：是否是保存并跳转到编辑页
	 * @return boolean
	 */
	public static function isTypeSave()
	{
		return self::getType() === self::TYPE_SAVE;
	}

	/**
	 * 返回表单提交后跳转方式：是否是保存并跳转到列表页
	 * @return boolean
	 */
	public static function isTypeSaveClose()
	{
		return self::getType() === self::TYPE_SAVE_CLOSE;
	}

	/**
	 * 返回表单提交后跳转方式：是否是保存并跳转到新增页
	 * @return boolean
	 */
	public static function isTypeSaveNew()
	{
		return self::getType() === self::TYPE_SAVE_NEW;
	}

	/**
	 * 判断是否是提交新增或编辑表单
	 * @return boolean
	 */
	public static function isPost()
	{
		return Ap::getRequest()->getParam('do') === 'post';
	}

	/**
	 * 验证是否是批量提交
	 * @return boolean
	 */
	public static function isBatch()
	{
		$isBatch = Ap::getRequest()->getInteger('is_batch');
		return ($isBatch === 1);
	}

	/**
	 * 获取ID值，如果是批量提交，则ID为英文逗号分隔的字符串
	 * @return mixed
	 */
	public static function getPk()
	{
		if ($this->isBatch()) {
			$ids = Ap::getRequest()->getTrim('ids');
			$ids = explode(',', $ids);
			return $ids;
		}

		return Ap::getRequest()->getInteger('id');
	}
}

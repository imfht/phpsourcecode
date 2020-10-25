<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace builders\services;

use builders\library\Lang;

/**
 * DataBuilders class file
 * 业务层：数据管理类，寄存常量、选项
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataBuilders.php 1 2014-05-26 19:25:19Z Code Generator $
 * @package builders.services
 * @since 1.0
 */
class DataBuilders
{
	/**
	 * @var string 是否生成扩展表：y
	 */
	const TBL_PROFILE_Y = 'y';

	/**
	 * @var string 是否生成扩展表：n
	 */
	const TBL_PROFILE_N = 'n';

	/**
	 * @var string 表引擎：MyISAM
	 */
	const TBL_ENGINE_MYISAM = 'MyISAM';

	/**
	 * @var string 表引擎：InnoDB
	 */
	const TBL_ENGINE_INNODB = 'InnoDB';

	/**
	 * @var string 表编码：utf8
	 */
	const TBL_CHARSET_UTF8 = 'utf8';

	/**
	 * @var string 表编码：gbk
	 */
	const TBL_CHARSET_GBK = 'gbk';

	/**
	 * @var string 表编码：gb2312
	 */
	const TBL_CHARSET_GB2312 = 'gb2312';

	/**
	 * @var string 代码类型：dynamic
	 */
	const SRV_TYPE_DYNAMIC = 'dynamic';

	/**
	 * @var string 代码类型：normal
	 */
	const SRV_TYPE_NORMAL = 'normal';

	/**
	 * @var string 移至回收站：y
	 */
	const TRASH_Y = 'y';

	/**
	 * @var string 移至回收站：n
	 */
	const TRASH_N = 'n';

	/**
	 * @var string 数据列表每行操作Btn：pencil
	 */
	const INDEX_ROW_BTNS_PENCIL = 'pencil';

	/**
	 * @var string 数据列表每行操作Btn：trash
	 */
	const INDEX_ROW_BTNS_TRASH = 'trash';

	/**
	 * @var string 数据列表每行操作Btn：remove
	 */
	const INDEX_ROW_BTNS_REMOVE = 'remove';

	/**
	 * 获取“是否生成扩展表”所有选项
	 * @return array
	 */
	public static function getTblProfileEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::TBL_PROFILE_Y => Lang::_('SRV_ENUM_GLOBAL_YES'),
				self::TBL_PROFILE_N => Lang::_('SRV_ENUM_GLOBAL_NO'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“表引擎”所有选项
	 * @return array
	 */
	public static function getTblEngineEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::TBL_ENGINE_MYISAM => Lang::_('SRV_ENUM_BUILDERS_TBL_ENGINE_MYISAM'),
				self::TBL_ENGINE_INNODB => Lang::_('SRV_ENUM_BUILDERS_TBL_ENGINE_INNODB'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“表编码”所有选项
	 * @return array
	 */
	public static function getTblCharsetEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::TBL_CHARSET_UTF8 => Lang::_('SRV_ENUM_BUILDERS_TBL_CHARSET_UTF8'),
				self::TBL_CHARSET_GBK => Lang::_('SRV_ENUM_BUILDERS_TBL_CHARSET_GBK'),
				self::TBL_CHARSET_GB2312 => Lang::_('SRV_ENUM_BUILDERS_TBL_CHARSET_GB2312'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“代码类型”所有选项
	 * @return array
	 */
	public static function getSrvTypeEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::SRV_TYPE_DYNAMIC => Lang::_('SRV_ENUM_BUILDERS_SRV_TYPE_DYNAMIC'),
				self::SRV_TYPE_NORMAL => Lang::_('SRV_ENUM_BUILDERS_SRV_TYPE_NORMAL'),
			);
		}

		return $enum;
	}

	/**
	 * 获取“移至回收站”所有选项
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

	/**
	 * 获取“数据列表每行操作Btn”所有选项
	 * @return array
	 */
	public static function getIndexRowBtnsEnum()
	{
		static $enum = null;

		if ($enum === null) {
			$enum = array(
				self::INDEX_ROW_BTNS_PENCIL => Lang::_('SRV_ENUM_BUILDERS_INDEX_ROW_BTNS_PENCIL'),
				self::INDEX_ROW_BTNS_TRASH => Lang::_('SRV_ENUM_BUILDERS_INDEX_ROW_BTNS_TRASH'),
				self::INDEX_ROW_BTNS_REMOVE => Lang::_('SRV_ENUM_BUILDERS_INDEX_ROW_BTNS_REMOVE'),
			);
		}

		return $enum;
	}
}

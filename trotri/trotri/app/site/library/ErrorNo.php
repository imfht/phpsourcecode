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

/**
 * ErrorNo class file
 * 常用错误码类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ErrorNo.php 1 2013-05-18 14:58:59Z huan.song $
 * @package library
 * @since 1.0
 */
class ErrorNo
{
	/**
	 * @var integer OK
	 */
	const SUCCESS_NUM                  = 0;

	/**
	 * @var integer 参数错误
	 */
	const ERROR_REQUEST                = 400;

	/**
	 * @var integer 用户没有访问权限
	 */
	const ERROR_NO_POWER               = 403;

	/**
	 * @var integer 用户未登录，禁止访问
	 */
	const ERROR_NO_LOGIN               = 404;

	/**
	 * @var integer 系统运行异常
	 */
	const ERROR_SYSTEM_RUN_ERR         = 500;

	/**
	 * @var integer 脚本运行失败
	 */
	const ERROR_SCRIPT_RUN_ERR         = 501;

	/**
	 * @var integer 未知错误
	 */
	const ERROR_UNKNOWN                = 2008;

}

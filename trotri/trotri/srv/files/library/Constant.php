<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace files\library;

/**
 * Constant class file
 * 常用常量类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Constant.php 1 2013-04-05 01:08:06Z huan.song $
 * @package files.library
 * @since 1.0
 */
class Constant
{
	/**
	 * @var string 上传图片配置：文档管理
	 */
	const POSTS_CLUSTER = 'posts';

	/**
	 * @var string 批量上传配置：系统管理
	 */
	const SYSBATCH_CLUSTER = 'sysbatch';

	/**
	 * @var string 上传图片|Flash配置：广告管理
	 */
	const ADVERTS_CLUSTER = 'adverts';

	/**
	 * @var string 上传图片配置：会员头像管理
	 */
	const HEAD_PORTRAIT_CLUSTER = 'head_portrait';

}

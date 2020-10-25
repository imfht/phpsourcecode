<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\model;

use libapp\BaseModel;
use libsrv\Service;

/**
 * Categories class file
 * 文档类别管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Categories.php 1 2013-04-20 17:11:06Z huan.song $
 * @package modules.posts.model
 * @since 1.0
 */
class Categories extends BaseModel
{
	/**
	 * 通过主键，查询一条记录
	 * @param integer $catId
	 * @return array
	 */
	public function findByPk($catId)
	{
		$row = $this->getService()->findByPk($catId);
		return $row;
	}

	/**
	 * 获取文档类别业务处理类
	 * @return posts\services\Categories
	 */
	public function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Categories', 'posts');
		}

		return $service;
	}
}

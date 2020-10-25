<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\topic\model;

use libapp\BaseModel;
use libsrv\Service;
use library\PageHelper;

/**
 * Topic class file
 * 专题管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Topic.php 1 2013-04-20 17:11:06Z huan.song $
 * @package modules.topic.model
 * @since 1.0
 */
class Topic extends BaseModel
{
	/**
	 * 查询多条记录
	 * @param integer $paged
	 * @return array
	 */
	public function findRows($paged = 0)
	{
		$paged = max((int) $paged, 1);
		$limit = PageHelper::getListRows();
		$offset = PageHelper::getFirstRow($paged, $limit);

		$rows = $this->getService()->findRows(array(), '', $limit, $offset, 'SQL_CALC_FOUND_ROWS');
		return $rows;
	}

	/**
	 * 通过专题Key，查询一条记录
	 * @param string $topicKey
	 * @return array
	 */
	public function findByTopicKey($topicKey)
	{
		$row = $this->getService()->findByTopicKey($topicKey, true);
		return $row;
	}

	/**
	 * 获取专题业务处理类
	 * @return \topic\services\Topic
	 */
	public function getService()
	{
		static $service = null;
		if ($service === null) {
			$service = Service::getInstance('Topic', 'topic');
		}

		return $service;
	}
}

<?php

namespace common\db;

use yii\db\ActiveQuery;

/**
 * 扩展结构查询基类
 *
 * @author ken <vb2005xu@qq.com>
 */
class BaseActiveQuery extends ActiveQuery
{

	/**
	 * 数据分页
	 * @param int $page 页码
	 * @param int $page_size 每页记录大小
	 * @return \common\db\BaseActiveQuery
	 */
	public function page($page, $page_size)
	{
		$offset = ($page - 1) * $page_size;
		$this->limit = $page_size;
		$this->offset = $offset;
		return $this;
	}

}

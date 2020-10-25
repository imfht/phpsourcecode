<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Database\Sql\Schema\Builders;
use Tang\Database\Sql\Schema\Columns;

/**
 * Mysql表结构l构建器
 * Class Mysql
 * @package Tang\Database\Sql\Schema\Builders
 */
class Mysql extends Builder
{
	protected function columnsHandle($results)
	{
		$cloumns = new Columns();
		foreach ($results as $value)
		{
			$cloumns->addColumn($value['Field'],strtolower($value['Key']) == 'pri',strtolower($value['Extra']) == 'auto_increment');
		}
		return $cloumns;
	}
}
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
 * Pgsql表结构构造器
 * Class Pgsql
 * @package Tang\Database\Sql\Schema\Builders
 */
class Pgsql extends Builder
{
	protected function columnsHandle($results)
	{
		$columns = new Columns();
		foreach ($results as $key => $value)
		{
            $incrementing = $value['default'] && strpos($value['default'], 'nextval') === 0;
			$columns->addColumn($value['columnName'],$value['columnKey'],$incrementing);
			if($incrementing)
			{
				$columns->setSequence(substr(substr($value['default'], 9),0, -2));
			}
		}
		return $columns;
	}
}
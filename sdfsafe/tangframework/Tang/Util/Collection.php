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
namespace Tang\Util;
use JsonSerializable;
use ArrayIterator;
use Tang\Interfaces\IToArray;

/**
 * 集合
 * Class Collection
 * @package Tang\Util
 */
class Collection extends ArrayIterator implements JsonSerializable,IToArray
{
	public function first()
	{
		foreach ($this as $key => $value)
		{
			return $value;
		}
	}
	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}
	public function toArray()
	{
		return $this->getArrayCopy();
	}
}
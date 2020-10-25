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
namespace Tang\Database\Sql\Exceptions;
use Tang\Exception\SystemException;

/**
 * SQL查询语句异常
 * Class QueryException
 * @package Tang\Database\Sql\Exceptions
 */
class QueryException extends SystemException
{
	protected $sql;
	protected $bindings;
	public function __construct($error,$sql,$bindings = array())
	{
		$this->sql = $sql;
		$this->bindings = $bindings;
		parent::__construct('Query SQL error',array($error),40003,'SQL');
	}
	protected function getOtherMessage()
	{
		$message =  '<b>sql:</b>'.$this->sql.'。<br><br>';
		if($this->bindings)
		{
			$message .= '<b>bindings: </b><br><pre>'.var_export($this->bindings,true).'</pre><br>';
		}
		return $message;
	}
}
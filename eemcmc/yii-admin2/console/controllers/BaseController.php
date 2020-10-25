<?php

namespace console\controllers;

use console\models\Autotask;
use common\helpers\String;

/**
 * 控制台基类
 * @author ken <vb2005xu@qq.com>
 */
abstract class BaseController extends \yii\console\Controller
{

	/**
	 * 成功信息
	 * @param string $msg
	 * @return string
	 */
	public function success($msg)
	{
		echo "{$msg}\n";
		return 0;
	}

	/**
	 * 失败信息
	 * @param string $msg 错误信息
	 * @param int $code 错误代码
	 * @return array
	 */
	public function failure($msg, $code = 1)
	{
		echo "Error: {$msg}\n";
		return $code;
	}

}

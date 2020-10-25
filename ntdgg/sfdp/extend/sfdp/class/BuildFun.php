<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace sfdp;

use think\Db;
use think\facade\Config;
use think\Exception;

require_once FILE_PATH . '/config/config.php';

class BuildFun{
	/**
     * 创建数据表
     */
    public function Bfun($data,$name)
    {
		$fileName = 'static' . DIRECTORY_SEPARATOR . 'sfdp' . DIRECTORY_SEPARATOR .'user-defined'. DIRECTORY_SEPARATOR .$name.'.js';
		return file_put_contents($fileName, $data);
		return ['info'=>'创建成功！','code'=>0];
    }
}
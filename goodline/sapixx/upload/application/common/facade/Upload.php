<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 文件管理-分层控制器
 */
namespace app\common\facade;
use think\Facade;

class Upload extends Facade{

    protected static function getFacadeClass()
    {
    	return 'app\common\facade\library\Upload';
    }
}
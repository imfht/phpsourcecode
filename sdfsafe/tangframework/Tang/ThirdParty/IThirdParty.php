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
namespace Tang\ThirdParty;
/**
 * 第三方接口
 * Interface IThirdParty
 * @package Tang\ThirdParty
 */
interface IThirdParty
{
    /**
     * 载入第三方库
     * 如果在框架中未找寻到 会在应用程序目录中找取
     * @param $name 名称
     * @param string $ext 后缀名
     * @return mixed
     */
    public function import($name,$ext='.php');

    /**
     * 设置应用目录
     * @param $directory
     * @return mixed
     */
    public function setAppDirectory($directory);

    /**
     * 设置框架目录
     * @param $directory
     * @return mixed
     */
    public function setFrameworkDirectory($directory);
}
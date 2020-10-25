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
 * 第三方工具导入实现
 * Class ThirdParty
 * @package Tang\ThirdParty
 */
class ThirdPartyImport implements IThirdParty
{
    protected $loadFiles = array();
    protected $frameworkThirdPartyDirectory;
    protected $appThirdPartyDirectory;

    public function setAppDirectory($directory)
    {
        $this->appThirdPartyDirectory = $directory.'Lib'.DIRECTORY_SEPARATOR.'ThirdParty'.DIRECTORY_SEPARATOR;
    }

    public function setFrameworkDirectory($directory)
    {
        $this->frameworkThirdPartyDirectory= $directory.'ThirdParty'.DIRECTORY_SEPARATOR;
    }

    public function import($name,$ext='.php')
    {
        $name = trim($name,'.');
        $id = $name .'||'. $ext;
        if(isset($this->loadFiles[$id]) && $this->loadFiles[$id])
        {
            return;
        }
        $name = str_replace('.',DIRECTORY_SEPARATOR,$name).$ext;
        $fileIsExists = false;
        //先载入框架第三方包
        $file = $this->frameworkThirdPartyDirectory.$name;
        if(file_exists($file))
        {
            $fileIsExists = true;
        } else if(file_exists($file=$this->appThirdPartyDirectory.$name))
        {
            $fileIsExists = true;
        }
        if($fileIsExists)
        {
            $this->loadFiles[$id] = true;
            include $file;
        }
    }
}
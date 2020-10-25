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
namespace Tang\Response;
/**
 * Response接口
 * Interface IResponse
 * @package Tang\Response
 */
interface IResponse
{
    /**
     * 写入数据
     * @param $string
     * @return void
     */
    public function write($string);

    /**
     * 输出数组
     * @param $array
     * @return void
     */
    public function writeArray($array);

    /**
     * 输出并且换行
     * @param $string
     * @return void
     */
    public function writeLine($string);
}
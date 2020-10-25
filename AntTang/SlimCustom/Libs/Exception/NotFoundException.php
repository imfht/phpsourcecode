<?php
/**
 * @package     NotFoundException.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年8月8日
 */

namespace SlimCustom\Libs\Exception;

use SlimCustom\Libs\Exception\SlimCustomException;

class NotFoundException extends SlimCustomException
{

    /**
     * 获取响应
     *
     * @return \SlimCustom\Libs\Http\Response
     */
    public function getResponse()
    {
        return response()->withStatus(404)->error($this->getCode(), $this->getMessage());
    }
}
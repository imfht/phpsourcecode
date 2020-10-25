<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */

namespace SlimCustom\Libs\Exception;

use Exception;

/**
 * Stop Exception
 *
 * This Exception is thrown when the Slim application needs to abort
 * processing and return control flow to the outer PHP script.
 */
class SlimCustomException extends Exception
{
    /**
     * 初始化
     *
     * @param $message [optional]            
     * @param $code [optional]            
     * @param $previous [optional]            
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 获取响应
     * 
     * @return \SlimCustom\Libs\Http\Response
     */
    public function getResponse()
    {
        return response()->error($this->getCode(), $this->getMessage());
    }
}

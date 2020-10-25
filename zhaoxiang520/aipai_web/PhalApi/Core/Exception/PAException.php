<?php
namespace PhalApi\Core\Exception;
use PhalApi\Core\Config;

/**
 * 自定义异常抛出[个性化定制页面样式]
 * @since   2016-08-28
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
class PAException extends \Exception {

    public function __construct($message, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        $e = [];
        if (Config::get('DEBUG')) {
            //调试模式下输出错误信息
            $e['message']   = $this->message;
            $e['file']      = $this->file;
            $e['line']      = $this->line;
            $e['trace']     = $this->getTraceAsString();
            // 包含异常页面模板
            include DOCUMENT_ROOT.'/PhalApi/Core/Exception/Exception.tpl';
            exit;
        } else {
            //否则定向到错误页面
            $e['message']   = Config::get('ERROR_MESSAGE');
            // 包含异常页面模板
            include DOCUMENT_ROOT.'/PhalApi/Core/Exception/Error.tpl';
            exit;
        }

    }

}

<?php
namespace herosphp\core;
/**
 * 应用程序错误类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

class AppError {

    /**
     * 错误代码，0表示没有错误，其他表示程序出错
     * @var
     */
    private $code;

    /**
     * 错误信息
     * @var
     */
    private $message;

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }



}
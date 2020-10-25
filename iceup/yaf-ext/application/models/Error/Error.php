<?php

namespace Error;

/**
 * 错误控制器
 */
class ErrorModel {

    /**
     * 抛出错误
     * 
     * @param int $code
     * @param string $message
     * @throws \Exception
     */
    public static function throwException($code, $message = null) {
        if (!$message) {
            $codeConfig = \Error\CodeConfigModel::getCodeConfig();
            if (empty($codeConfig[$code])) {
                throw new \Exception('错误码' . $code . '的相应提示信息没有设置');
            }
            $message = $codeConfig[$code];
        }

        throw new \Error\OurExceptionModel($message, $code);
    }

}

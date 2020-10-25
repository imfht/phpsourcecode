<?php

namespace Error;

/**
 * 错误码设置类
 */
class CodeConfigModel {

    /**
     * 获取错误码配置
     */
    public static function getCodeConfig() {
        return array(
            //100xxx：用户
            "100110" => "账号不存在",
        );
    }

}

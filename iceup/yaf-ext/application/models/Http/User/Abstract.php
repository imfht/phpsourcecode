<?php

namespace Http\User;

/**
 * 用户中心 http数据来源抽象类
 */
class AbstractModel extends \Http\AbstractModel {

    /**
     * 构造函数，实现初始化host
     */
    public function __construct() {
        $this->setHost(\Bootstrap::getUrlIniConfig("userHost"));
    }

}

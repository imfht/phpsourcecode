<?php

/**
 * 配置文件测试
 */
class ConfigController extends \Our\Controller_AbstractApi {

    public function readAction() {
        var_dump(\Yaf\Registry::get('config'));
    }

}

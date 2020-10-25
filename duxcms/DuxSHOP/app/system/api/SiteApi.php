<?php

/**
 * 站点信息
 */

namespace app\system\api;

use \app\base\api\BaseApi;

class SiteApi extends BaseApi {

    /**
     * 获取系统信息
     */
    public function info() {
        $configList = target('system/SystemInfo')->loadList();
        $data = array();
        foreach($configList as $vo) {
            $data[$vo['key']] = $vo['value'];
        }
        $this->success($data);
    }

}
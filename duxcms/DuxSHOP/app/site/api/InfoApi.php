<?php

/**
 * 站点信息
 */

namespace app\site\api;

use \app\base\api\BaseApi;

class InfoApi extends BaseApi {

    /**
     * 站点设置信息
     */
    public function index() {
        $config = target('site/SiteConfig')->getConfig();
        $this->success('ok', $config);
    }
}
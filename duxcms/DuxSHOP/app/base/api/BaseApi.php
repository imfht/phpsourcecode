<?php

/**
 * 基础API
 */

namespace app\base\api;

class BaseApi extends \dux\kernel\Api {

    protected $sysInfo;
    protected $sysConfig;
    protected $apiConfig;
    protected $apiType = 'app';

    public function __construct() {
        parent::__construct();
        $this->sysInfo = \dux\Config::get('dux.info');
        $this->sysConfig = \dux\Config::get('dux.use');
        $this->apiConfig = load_config('data/config/use/api');
        $this->checkLink();
        target('system/Statistics', 'service')->incStats('api');

    }

    /**
     * 检查链接码
     */
    private function checkLink() {
        $token = $_SERVER['HTTP_TOKEN'];
        $label = $_SERVER['HTTP_LABEL'];
        $key = $this->sysConfig['com_key'];
        if(!empty($label) && $this->apiConfig[$label]) {
            $key = $this->apiConfig[$label]['key'];
            $this->apiType = $this->apiConfig[$label]['type'];
        }
        if ($key <> $token) {
            $this->error('Link Error code', 403);
        }
    }


    /**
     * 分页数据
     * @param $pageLimit
     * @param $list
     * @param $pageData
     * @return array
     */
    protected function pageData($pageLimit, $list, $pageData) {
        return [
            'limit' => $pageLimit,
            'page' => $pageData['page'],
            'totalPage' => $pageData['totalPage']
        ];
    }

}
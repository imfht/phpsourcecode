<?php

/**
 * 搜索API
 */

namespace app\site\api;

class SearchApi extends \dux\kernel\Api {

    public function index() {
        $where = [];
        $limit = 10;
        $order = 'num desc';
        if($this->data['app']) {
            $where['app'] = \dux\lib\Str::symbolClear($this->data['app']);
        }
        if($this->data['limit']) {
            $limit = intval($this->data['limit']);
        }
        if($this->data['order']) {
            $order = html_clear($this->data['order']);
        }
        $list = target('site/SiteSearch')->loadList($where, $limit, $order);
        $this->success('ok', $list);
    }

}
<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

namespace traits;

use hope\Request;

trait Jump
{
    /**
     * 成功提示
     * @param string $msg
     * @param null $url
     * @param string $data
     * @param int $wait
     */
    protected function success($msg = '', $url = null, $data = '', $wait = 3)
    {
        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if (Request::instance()->isPost()) {
            header('Content-type: application/json; charset=utf-8');
            die(json_encode($result));
        } else {
            die(include HOPE_PATH . 'temp/jump.php');
        }
    }

    /**
     * 错误提示
     * @param string $msg
     * @param null $url
     * @param string $data
     * @param int $wait
     */
    protected function error($msg = '', $url = null, $data = '', $wait = 3)
    {
        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if (Request::instance()->isPost()) {
            header('Content-type: application/json; charset=utf-8');
            die(json_encode($result));
        } else {
            die(include HOPE_PATH . 'temp/jump.php');
        }
    }

    /**
     * 返回封装数据
     * @param $data
     * @param int $code
     * @param string $msg
     */
    protected function result($data, $code = 0, $msg = '')
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        header('Content-type: application/json; charset=utf-8');
        die(json_encode($result));
    }

    /**
     * 重定向url
     * @param $url
     */
    protected function redirect($url)
    {
        die(header("Location: {$url}"));
    }
}
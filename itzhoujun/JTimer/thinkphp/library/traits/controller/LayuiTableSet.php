<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28
 * Time: 16:53
 */

namespace traits\controller;

use think\Config;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

trait LayuiTableSet
{

    protected function tableSet($data, $count = 0, $code = 0, $msg = ''){
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
            'count' => $count
        ];
        $type     = $this->getResponseType();
        $response = Response::create($result, $type);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return Request::instance()->isAjax()
            ? Config::get('default_ajax_return')
            : Config::get('default_return_type');
    }
}
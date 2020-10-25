<?php
/**
 *
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-03-27 00:15:31
 * @version $Id$
 */
namespace app\api\controller;

use think\restful\ApiReponse;

class Index
{
    public function index()
    {
        return ApiReponse::json(404, 'No Found',['error' => 'No Found', 'code' => 404]);
    }
}

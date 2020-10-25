<?php

/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/23
 * Time: 19:43
 */

namespace Yan\Core;


class ReturnCode
{
    //系统错误------------------------------------------------------------
    const OK                                    = 0;
    const SYSTEM_ERROR                          = -1; //系统错误
    const METHOD_NOT_EXIST                      = -2; //方法不存在
    const DB_ERROR                              = -3; //数据库查询异常

    //逻辑错误------------------------------------------------------------
    const REQUEST_404                           = 5001; //请求404
    const REQUEST_METHOD_NOT_ALLOW              = 5002; //请求方法不允许
    const INVALID_ARGUMENT                      = 5003; //参数错误

}
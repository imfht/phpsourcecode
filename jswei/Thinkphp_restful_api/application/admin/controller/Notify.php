<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/8/18
 * Time: 16:08
 */

namespace app\admin\controller;

class Notify{
    static  $Notify_成功 = 200;
    static  $Notify_请求失败 = 201;
    static  $Notify_错误请求方式 = 202;
    static  $Notify_参数错误 = 203;
    static  $Notify_缺少参数 = 204;
    static  $Notify_Token为空 = 205;
    static  $Notify_Token错误 = 206;
    static  $Notify_Token失效 = 207;
    static  $Notify_出错了 = 208;
    static  $Notify_非法请求原 = 209;
    static  $Notify_请求已超时 = 210;
    static $Notify_签名错误= 2000;

    static $Push_个推通知 = [
        'ok' =>5001,                                 //推送成功
        'TokenMD5NoUsers'=>5002,                    //推送用户不存在
        'no_msg' =>5003,                             //没有消息体
        'alias_error' =>5003,                       //找不到别名
        'black_ip' =>5004,                          //黑名单ip
        'sign_error' =>5003,                        //鉴权失败
        'pushnum_overlimit' =>5004,                 //推送次数超限
        'no_appid' =>5005,                          //找不到appid
        'no_user' =>5006,                           //找不到对应用户
        'too_frequent' =>5007,                      //推送过于频繁
        'sensitive_word' =>5008,                    //有敏感词出现
        'appid_notmatch' =>5009,                    //appid与cid或者appkey不匹配
        'not_auth' =>5010,                          //用户没有鉴权
        'black_appid' =>5011,                       //黑名单app
        'invalid_param' =>5012,                     //参数检验不通过
        'alias_notbind' =>5013,                      //别名没有绑定cid
        'tag_over_limit' =>5014,                     //tag个数超限
        'successed_online' =>5015,                  //在线下发
        'successed_offline' =>5016,                 //离线下发
        'taginvalid_or_noauth' =>5017,              //tag无效或者没有使用权限
        'no_valid_push' =>5018,                     //没有有效下发
        'successed_ignore' =>5019,                  //忽略非活跃用户
        'no_taskid' =>5020,                         //找不到taskid
        'other_error' =>5021,                        //其他错误
    ];

}
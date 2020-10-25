<?php
/**
 * cache缓存的key值前缀
 * Date: 2018\3\31 0031 13:50
 */

namespace beans;
class CachePrefix
{
    /* rest 登录缓存 */
    const LOGIN_TOKEN = 'auth_';
    /* 用户登录后个人信息前缀 */
    const DB_AUTH_UID = 'auth_uid_';

    /*小程序视频详情小程序码*/
    const WEAPP_QR_VIDEO_DETAIL = 'weapp_qr_video_detail';
}
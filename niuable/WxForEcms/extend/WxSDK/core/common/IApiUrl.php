<?php
namespace WxSDK\core\common;

/**
 * 配合IAccessToken 组装完整url
 * @author 97893
 *
 */
interface IApiUrl{
    public function getUrl(String $accessToken);
}
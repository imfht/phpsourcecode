<?php
/**
 * Created by PhpStorm.
 * User: yingouqlj
 * Date: 17/1/13
 * Time: 下午5:15
 */

namespace extend\weapp\api;


class MediaGet extends BaseApi
{
    const API = 'https://api.weixin.qq.com/cgi-bin/media/get';
    const NEED_ACCESS_TOKEN = true;


    public function getMedia($mediaId)
    {
        $params = [
            'access_token' => $this->accessToken,
            'media_id' => $mediaId,
        ];
        $result = $this->query(self::API, $params);

        return $result;
    }


}
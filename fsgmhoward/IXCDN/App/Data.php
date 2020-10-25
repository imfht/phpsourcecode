<?php
/**
 * This file is part of IXCDN
 * Published under MIT License
 * Copyright (c) Howard Liu, 2016
 * Please refer to LICENSE file for more information
 */

namespace App;

use IXNetwork\Lib\Tool\MultiCURL as CURL;

class Data
{
    public static function get($url)
    {
        $ch = CURL::createHandle($url, [
            CURLOPT_HTTPHEADER => [
                "User-Agent: ".$_SERVER['HTTP_USER_AGENT']
            ]
        ]);
        $result = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return [$result, $code, $contentType];
    }
}

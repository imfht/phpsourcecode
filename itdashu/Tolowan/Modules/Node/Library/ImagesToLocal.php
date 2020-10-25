<?php
namespace Modules\Node\Library;

use Modules\Queue\Library\Common as Qcommon;

class ImagesToLocal
{
    public static $output = array();

    public static function to($body)
    {
        $body = preg_replace_callback("/(src|SRC|href|HREF)=[\"|'| ]{0,}(http(.*).(gif|jpg|jpeg|bmp|png))(['|\"]+)/isU", 'self::imageRe', $body);
        Qcommon::put('imgToLocal', serialize(self::$output));
        self::$output = null;
        return $body;
    }
    public static function imageRe($matches)
    {
        if (strtolower($matches[1]) == 'href') {
            return;
        }
        global $di;
        $imgUrl = parse_url($matches[2]);
        $urlInfo = pathinfo($matches[2]);
        $httpHost = $di->getShared('request')->getHttpHost();
        if (!isset($urlInfo['extension']) && $imgUrl['host'] != $httpHost) {
            return;
        }
        $filename = '/images' . date('/Y/m/d/') . time() . '.' . $urlInfo['extension'];
        self::$output[] = array('url' => $matches[2], 'file' => 'public' . $filename);
        return 'src="' . $filename . '"';
    }
}

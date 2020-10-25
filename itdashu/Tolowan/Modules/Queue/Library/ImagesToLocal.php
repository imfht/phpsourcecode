<?php
namespace Modules\Queue\Library;

use Core\Config;
use Core\HttpClient;
use Core\File;

class ImagesToLocal
{
    public static $output = array();

    public static function to($body)
    {
        $newBody = preg_replace_callback("/<img.*?(src|SRC|href|HREF)=[\"|'| ]{0,}(http.*?\.(gif|jpg|jpeg|bmp|png))['|\"]{0,}.*?>/", 'self::imageRe', $body);
        //Qcommon::put('imgToLocal', serialize(self::$output));
        if (Queue::add('imgToLocal', self::$output)) {
            return $newBody;
        }
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
        if (!isset($urlInfo['extension']) || $imgUrl['host'] == $httpHost || !isset($urlInfo['filename'])) {
            return '';
        }
        $newFileName = md5($urlInfo['filename']);
        $fileDir = 'file/' . $urlInfo['extension'] . date('/Y/m/d/');
        $extension = $urlInfo['extension'];
        $filename = '/' . $fileDir . $newFileName . '.' . $extension;
        self::$output[] = [
            'url' => $matches[2],
            'file' => WEB_CODE.'/'.$fileDir . $newFileName . '.' . $extension
        ];
        return '<img src="' . $filename . '" />';
    }

    public static function imgToLocal($data)
    {
        if (!is_array($data)) {
            return '数据不合法，参数应该是数组';
        }
        if (!isset($data['url'])) {
            foreach ($data as $d) {
                if (is_array($d) && isset($d['url']) && isset($d['file'])) {
                    self::_imgToLocal($d);
                }
            }
        } elseif (isset($data['url']) && isset($data['file'])) {
            self::_imgToLocal($data);
        }
        return true;
    }

    public static function _imgToLocal($data)
    {
        File::mkdir(dirname($data['file']));
        $http = new HttpClient();
        $http->request($data['url'], array(
            'stream' => true,
            'blocking' => true,
            'timeout' => 30,
            'filename' => ROOT_DIR.$data['file']
        ));
    }
}

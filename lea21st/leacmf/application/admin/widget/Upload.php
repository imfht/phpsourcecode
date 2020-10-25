<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/31
 * Time: 15:21
 */

namespace app\admin\widget;

use app\common\library\Qiniu;
use think\facade\Config;
use think\facade\View;

class Upload
{

    public function editor($width = '100%', $height = "300px", $name = "content")
    {
        return View::fetch('widget/editor', [
            'width'  => $width,
            'height' => $height,
            'name'   => $name,
        ]);
    }

    public function upload($field = 'file', $type = 'image', $value = '')
    {
        $param['type']  = $type;
        $param['field'] = $field;

        $param['single'] = substr($field, -2) == '[]' ? '' : 1;

        $param['time'] = uniqid();
        $param['value'] = $value;
        return View::fetch('widget/upload', $param);
    }
}
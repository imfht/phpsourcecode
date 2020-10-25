<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 二维码生成服务
 */
namespace app\common\widget;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use filter\Filter;

class Qrcode{

    /**
     * 根据内容生成二维码
     * @return string
     */
    public function create($url,$qrname = 'qrcode'){
        $qrname = PATH_RES.'qrcode'.DS.Filter::filter_escape($qrname).'.png';
        $renderer = new Png();
        $renderer->setHeight(256);
        $renderer->setWidth(256);
        $renderer->setMargin(1);
        $writer = new Writer($renderer,'N');
        $writer->writeFile($url,$qrname);
        return '/'.str_replace('\\','/',substr($qrname,strlen(PATH_PUBLIC)));
    }

    /**
     * 直接把内容保存到服务器
     * @return string
     */
    public function saveQcode($str,$qrname = 'qrcode'){
        $qrname = PATH_RES.'qrcode'.DS.Filter::filter_escape($qrname).'.png';
        file_put_contents($qrname,$str);
        return '/'.str_replace('\\','/',substr($qrname,strlen(PATH_PUBLIC))); 
    }
}
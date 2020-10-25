<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/6
 * Time: 10:34
 */

namespace naples\app\SysNaples\controller;


use naples\lib\base\Controller;
use naples\lib\Image;

class Qrcode extends Controller
{

    function index(){
        config('debug',false);
        $qr_content=isset($_GET['qr_content'])?$_GET['qr_content']:'null';
        $qr_content=\Yuri2::decrypt($qr_content);
        $fileName=md5($qr_content).'.png';
        $trueDirPath=PATH_RUNTIME.'/qrCode_cache';
        $fullPath=$trueDirPath.'/'.$fileName;
        if (is_file($fullPath)){
            $image=new Image($fullPath);
            $image->display();
        }else{
            $qr_content=unserialize($qr_content);
            $qr_water=!empty($qr_content['water'])?$qr_content['water']:'naples';
            \Yuri2::createDir($trueDirPath);
            \QRcode::png($qr_content['value'],$fullPath,QR_ECLEVEL_M,$qr_content['size'],$qr_content['margin']);
            $image=new Image($fullPath);
            $info=$image->getImageInfo();
            $w = round($info[0]/5); //生成的水印宽度
            $waterPng=PATH_NAPLES."/data/qrCodeWater/$qr_water.png";
            if (is_file($waterPng)){
                $objWater=new Image($waterPng);
                $newWaterPath=$trueDirPath.'/'.uniqid('qrcode_water').'png';
                $objWater->thumb($newWaterPath,$w);
                $image->water($fullPath,$newWaterPath,5,100);
                unlink($newWaterPath);
            }
            $image->display(!$qr_content['cache']);
        }
    }
}
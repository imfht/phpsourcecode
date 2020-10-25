<?php
namespace Scabish\Crab;

use Exception;

/**
 * Scabish\Crab\Client
 * Crab客户端
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-12-7
 */
class Client {
    
    private $url; // Crab服务地址
    
    private function __construct($url) {
    	$this->url = $url;
    }
    
    /**
     * 上传文件到Crab
     * @param string $file 文件绝对路径
     * @throws Exception
     */
    public function Upload($file) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['file' => '@'.$file]);
        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response);
        if($result && $result->status == 1) { // 上传成功
            print_r($result->data); // 获取文件存储地址url
        } else {
            throw new Exception($response);
        }    
    }
    
    /**
     * 获取原始图片对应的缩略图地址
     * @param string $origin 原图地址
     * @param number $width 缩略图宽
     * @param number $height 缩略图高
     * @return string
     */
    public static function Thumb($origin, $width = 0, $height = 0) {
        if(!($width || $height)) return $origin;
        $dotPos = strrpos($origin, '.');
        $url = substr($origin, 0, $dotPos);
        if($width) {
            $url .= '-'.$width;
            if($height) {
                $url .= '-'.$height;
            }
        } elseif($height){
            $url .= '---'.$height;
        }
        return $url .= substr($origin, $dotPos);
    }
    
    /**
     * 删除文件/图片
     * @param string|array $file 原始文件/图片地址(多个文件使用数组传递)
     */
    public static function Delete($file) {
        //@todo
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 16:17
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * 日志记录
 */
class Logger extends Service
{
    /**
     * 写日志
     * @param $var mixed 内容变量
     * @param $label string 标签
     * @param $level int 警报等级
     *
     */
    public function log($var,$label=null,$level=3){

        switch ($level){
            case 2:
                $color='rgb(255,255,230)'; //黄色
                break;
            case 1:
                $color='rgb(255,230,230)'; //红色
                break;
            default:
                $color='rgb(230,255,230)'; //绿色
                break;
        }
        $content='<br/><br/>'.\Yuri2::dump($var,false);
        $time='<span class="naples-title">'.getMilliDate().' ---- '.$label.'</span>';
        $div='<div class="naples-log naples-hide" style="background-color: '.$color.'" ondblclick="slideUp(this)" onclick="slideDown(this)">'.$time.$content.'</div>';
        $fileName=$this->config('savePath').'/'.date('Y-m-d').'.html';
        \Yuri2::createDir($this->config('savePath'));
        if (!is_file($fileName)){
            file_put_contents($fileName,self::getPreStr(),LOCK_EX);
        }
        file_put_contents($fileName,$div,FILE_APPEND|LOCK_EX);
    }

//    public function display(){
//        $dir=$this->config('savePath');
//        $dh=opendir($dir);
//        while ($file=readdir($dh)) {
//            if($file!="." && $file!="..") {
//                include $dir.'/'.$file;
//            }
//        }
//    }

    /**
     * 渲染日志文件框架
     * @return string
     */
    private static function getPreStr(){
        return <<<EOT
<head>
   <meta charset="utf-8">
</head>
<style>
  .naples-log{
    margin: 10px;background-color: rgb(255,230,230);padding:10px;
    max-height:1600px;
    transition: all 0.5s;overflow: auto;
  }
  .naples-hide{
    max-height:35px;overflow: hidden;cursor: pointer;
  }
  .naples-title{
    font-weight:bold;
  }
</style>
<div id='naplesLogContainer'></div>
<script>
  function slideUp(e) {
    if (e.getAttribute('class')=='naples-log'){
      e.setAttribute('class','naples-log naples-hide');
    }
  }
  function slideDown(e) {
    if (e.getAttribute('class')!='naples-log'){
      e.setAttribute('class','naples-log');
    }
  }
  window.onload=function () {
    var bigContainer = document.querySelectorAll(".naples-log");
    for(var i=bigContainer.length-1;i>-1;i--){
      document.querySelector("#naplesLogContainer").appendChild(bigContainer[i]);
    }
  }
</script>

EOT;

    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 16:16
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * 注释解析器
 * @author yuri2
 */
class DocParser extends Service
{
    private $rules = [];
    private $results=[];

    /** 加载配置 */
    function init(){
        $this->rules=require $this->config('rule');
    }

    /**
     * 解析注释字符串，返回数组形式的结果
     * @param $doc string
     * @return array
     */
    function parse($doc = '') {
        $lines=$this->getLines($doc);
        foreach ($lines as $line){
            $this->getRelLine($line);
        }
        return $this->results;
    }

    /**
     * 分解为行
     * @param $strDoc string
     * @return array
     */
    public function getLines($strDoc){
        if (!isFlagNotSet(cache('DocParser_getLines_'.$strDoc))){
            return cache('DocParser_getLines_'.$strDoc);
        }
        $arr=\Yuri2::explodeWithoutNull($strDoc,"\n");
        $lines=[];
        foreach ($arr as $line){
            $line=trim($line);
            $line=preg_replace("/^\/\*\*/",'',$line);//去掉 /**
            $line=preg_replace("/\*\/$/",'',$line); //去掉 */
            $line=preg_replace("/^\*/",'',$line);//去掉 *
            $line=trim($line);
            if ($line){$lines[]=$line;}
        }
        cache('DocParser_getLines_'.$strDoc,$lines);
        return $lines;
    }

    /**
     * 对每一行求结果
     * @param $line string
     */
    private function getRelLine($line){
        $arrLine=\Yuri2::explodeWithoutNull($line,' ');
        $name=array_shift($arrLine);
        if (isset($this->rules[$name])){
            $func=$this->rules[$name];
            $funReflect=new \ReflectionFunction($func);
            $rel=\Yuri2::invokeMethod($funReflect,$arrLine);
            $this->results[]=$rel;
        }
    }

    /**
     * 每一行中，@开头的被记录为关联数组
     * @param $lines array
     * @return array
     */
    public function linesToArray($lines){
        if (!is_array($lines)){return [];}
        $rel=[];
        foreach ($lines as $line){
            $lineArr=explode(' ',$line,2);
            if (count($lineArr)==2){
                $rel[ltrim($lineArr[0],'@')]=$lineArr[1];
            }
        }
        return $rel;
    }

}
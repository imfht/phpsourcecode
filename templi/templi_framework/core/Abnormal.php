<?php
/**
 * 异常处理类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-3-19
 */
namespace framework\core;

class Abnormal extends \Exception
{
    
    /*public function __construct($message, $code=0, $previous=true){
        parent::__construct($message, $code);
    }*/
    public function __toString(){
        
        $trace = $this->getTrace();
        array_shift($trace); //去除抛出异常处文件跟踪
        $traceInfo ='';
        $time =date('y-m-d H:i:s');
        foreach($trace as $key => $val){
            //print_r($val['args']);
            $traceInfo .= sprintf("#%d [%s] %s(%d) %s%s%s(%s)\n",
                        $key,
                        $time,
                        $val['file'],
                        $val['line'],
                        $val['class'],
                        $val['type'],
                        $val['function'],
                        implode(',', $val['args'])
                    );
        }
        return $traceInfo;
        
    }
}
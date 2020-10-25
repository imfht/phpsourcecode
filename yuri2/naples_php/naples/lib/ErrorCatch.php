<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:52
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * 错误处理类
 */
class ErrorCatch extends Service
{

    private  static $errors=[];//保存错误信息

    /** 初始化 决定要不要显示错误 */
    public function init(){
        if (config('debug')){
            ini_set('xdebug.var_display_max_children', 128);
            ini_set('xdebug.var_display_max_data', 2048);
            ini_set('xdebug.var_display_max_depth', 8);
        }
        if (config('turn_off_error_display')){
                error_reporting(0);
                ini_set('display_errors', 'Off');
        }else{
            error_reporting(E_ALL);
        }
    }

    /** 结束处理函数  */
    public static function onShutdown(){
        $error=error_get_last();
        if ($error){
            $msgArr=explode('\n',$error['message']);
            $msg=array_shift($msgArr);
            $errno=$error['type'];
            $type=self::errLevelMap($errno);
            $errstr=$msg;
            $errfile=$error['file'];
            $errline=$error['line'];
            $trace=[];
            self::$errors[]=[
                'errno'=>$errno,
                'type'=>$type,
                'msg'=>$errstr,
                'file'=>$errfile,
                'line'=>$errline,
                'trace'=>$trace
            ];
            if(self::isNeedErrLog($errno)){
                Factory::getLogger()->log(['type'=>$type,'msg'=>$errstr,'file'=>$errfile,'line'=>$errline],ID.' ---- '.$type,$errno);//记录错误信息
            }
        }


        if (self::$errors){
            if (config('debug')){
                self::displayError();
            }else{
                $isNeedAlert=false;
                foreach (self::$errors as $error){
                    $errno=$error['errno'];
                    if (self::isNeedErrLog($errno)){
                        $isNeedAlert=true;
                    }
                }
                if ($isNeedAlert){
                    header('HTTP/1.1 500 Internal Server Error');
                    error('页面运行发生错误');
                }
            }
        }
    }

    /**
     * error handle 错误处理
     * @param $errno int 错误级别
     * @param $errstr string 错误信息
     * @param $errfile string 错误文件
     * @param $errline int 错误行号
     */
    public static function onError($errno, $errstr, $errfile, $errline){
        $type=self::errLevelMap($errno);
        self::$errors[]=[
            'errno'=>$errno,
            'type'=>$type,
            'msg'=>$errstr,
            'file'=>$errfile,
            'line'=>$errline,
            'trace'=>debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
        ];
        if(self::isNeedErrLog($errno)){
            Factory::getLogger()->log(['type'=>$type,'msg'=>$errstr,'file'=>$errfile,'line'=>$errline],ID.' ---- '.$type,$errno);//记录错误信息
        }
    }

    /**
     * 异常处理
     * @param $exc \Exception
     */
    public static function onException($exc){
        $errno=$exc->getCode()?$exc->getCode():1;
        $type=self::errLevelMap($errno);
        $errstr=$exc->getMessage();
        $errfile=$exc->getFile();
        $errline=$exc->getLine();
        $trace=$exc->getTrace();
        self::$errors[]=[
            'errno'=>$errno,
            'type'=>$type,
            'msg'=>$errstr,
            'file'=>$errfile,
            'line'=>$errline,
            'trace'=>$trace
        ];
        if(self::isNeedErrLog($errno)){
            Factory::getLogger()->log(['type'=>$type,'msg'=>$errstr,'file'=>$errfile,'line'=>$errline],ID.' ---- '.$type,$errno);//记录错误信息
        }
    }

    /** 根据错误数目来改变按钮样式 */
    public static function displayError(){
        if (self::$errors and config('debug') and config('show_debug_btn')){
            $num=count(self::$errors);
            echo "<script>document.getElementById('naples-trace-btn').style.backgroundColor='#c83235';document.getElementById('naples-trace-btn').innerHTML+=' [$num]'</script>";
        }
        
    }

    /**
     * 错误代码对照表
     * @param $level int|string 级别
     * @return string
     */
    public static function errLevelMap($level){
        $map=[
            '1'=>'运行时致命的错误',
            '2'=>'运行时非致命的错误',
            '4'=>'编译时语法解析错误',
            '8'=>'运行时通知',
            '16'=>'PHP 初始化启动过程中发生的致命错误',
            '32'=>'PHP 初始化启动过程中发生的警告 ',
            '64'=>'致命编译时错误',
            '128'=>'编译时警告',
            '256'=>'用户产生的错误信息',
            '512'=>'用户产生的警告信息',
            '1024'=>'用户产生的通知信息',
            '2048'=>'PHP 对代码的修改建议',
            '4096'=>'可被捕捉的致命错误',
            '8192'=>'运行时通知',
            '16384'=>'用户产生的警告信息',
            '32767'=>'E_STRICT 触发的所有错误和警告信息',
        ];

        return isset($map[$level])?$map[$level]:'未知错误';

    }
    
    /**
     * 检查错误是否需要被记录
     * @param $errno 错误级别
     * @return bool
     */
    private static function isNeedErrLog($errno){
        $error_log_lv=config('error_log_lv')?config('error_log_lv'):0;
        $lv=pow(2,$error_log_lv);
        if ($errno<=$lv){
            return true;
        }else{
            return false;
        }
    }

    /** 获取所有错误 */
    public  static function getErrors(){
        return self::$errors;
    }

}
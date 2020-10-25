<?php
namespace Scabish\Core;

use SCS;
use Exception;

/**
 * Scabish\Core\Error
 * 系统错误捕捉类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-01-24
 */
class Error {
    
    public function __construct() {}
    
    /**
     * 
     * 系统错误处理
     * @param integer $code 错误状态码
     * @param string $message
     * @param string $file
     * @param integer $line
     * @param array $context
     */
    public static function ErrorHandler($code, $message, $file, $line, $context = null) {
        $trace = self::GetDebugTrace();
        self::Report(compact('code', 'message', 'file', 'line', 'trace'));
    }
    
    /**
     * 异常错误处理
     * @param Exception $e
     */
    public static function ExceptionHandler(Exception $e) {
        self::Report([
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'trace' =>self::GetDebugTrace()
        ]);
    }
    
    /**
     * 致命错误处理
     */
    public static function FatalHandler() {
        if(!is_null($e = error_get_last()) && in_array($e['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $messages = explode(PHP_EOL, $e['message']);
            $message = array_shift($messages);
            if($messages) unset($messages[0]);
            self::Report([
                'code' => $e['type'],
                'file' => $e['file'],
                'line' => $e['line'],
                'message' => $message,
                'trace' => implode(PHP_EOL, $messages)
            ]);
        }
    }
    
    /**
     * 错误报告
     * @param array $data debug信息
     */
    protected static function Report(array $data) {
        if($data['code'] == E_DEPRECATED) return;
        if(SCS::Instance()->debug) {
            if(SCS::Instance()->mode == 'web') { // web运行模式
                self::WebReport($data, true);
            } else { // cmd运行模式
                self::CmdReport($data, true);
            }
        } else {
            if(in_array($data['code'], array(E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED))) return;
            self::Log($data);
            if(SCS::Instance()->mode == 'web') { // web
                self::WebReport($data, false);
            } else { // cmd
                die($data['message'].PHP_EOL);
            }
        }
    }
    
    /**
     * web方式错误反馈
     * @param array $data
     * @param boolean $debug
     */
    protected static function WebReport($data, $debug) {
        if($debug) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/html; charset=utf-8');
            if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $msg = '<div style="font-size:14px;">';
                $msg .= '<strong>Code: </strong>'.self::Translate($data['code']).'<br>';
                $msg .= '<strong>Message: </strong><span style="color:red">'.$data['message'].'</span><br>';
                $msg .= '<strong>Line: </strong>'.$data['file'].'('.$data['line'].')<br>';
                $msg .= $data['trace'] ? '<strong>Trace: </strong>'.nl2br($data['trace']).'<br>' : '';
                $msg .= '<a href="http://git.oschina.net/pycorvn/Scabish" target="blank">Scabish '.SCS::VERSION.'</a>';
                $msg .= '</div>';
                echo $msg; exit;
            } else {
                echo $data['message']; exit;
            }   
        } else {
            if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                list($controller, $action) = explode('/', $data['code'] == E_USER_ERROR ? SCS::Instance()->error : SCS::Instance()->lost);
                $action = $action ? : 'Index';
                $controllerName = ucfirst($controller).'Controller';
                $control = new $controllerName;
                $control->$action($data);
            } else {
                echo $data['message']; exit;
            }
        }
    }
    
    /**
     * 命令终端错误反馈
     * @param array $data
     * @param boolean $debug
     */
    protected static function CmdReport($data, $debug) {
        if($debug) {
            $msg = 'Code: '.self::Translate($data['code']).PHP_EOL;
            $msg .= 'Message: '.$data['message'].PHP_EOL;
            $msg .= 'Line: '.$data['file'].'('.$data['line'].')'.PHP_EOL;
            $msg .= $data['trace'] ? 'Trace: '.nl2br($data['trace']).PHP_EOL : '';
            echo $msg.PHP_EOL; exit;
        } else {
            echo $data['message'].PHP_EOL; exit;
        }
    }
    
    /**
     * 记录异常信息到日志
     * @param array $log
     */
    protected static function Log(array $log) {
        if($log['code'] == E_DEPRECATED) return;
        if(false === SCS::Instance()->log) return;
        
        list($control, $action) = explode('/', SCS::Instance()->log);
        $action = $action ? : 'Index';
        $controlName = '\\Control\\'.ucfirst($control);
        
        $control = new $controlName;
        $control->$action($log);
    }
    
    /**
     * 错误代码翻译
     * @param integer $code
     * @return string
     */
    protected static function Translate($code) {
        $map = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            0 => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        );
        return isset($map[$code]) ? $map[$code] : $code;
    }
    
    /**
     * 获取debug跟踪信息
     */
    protected static function GetDebugTrace() {
        $traces = debug_backtrace();
        $trace = $traces[1];
        $MakeTraceString = function($traces) {
            foreach($traces as $k=>$v) {
                $params = [];
                $args = isset($v['args']) ? $v['args'] : [];
                foreach($args as $arg) {
                    $params[] = is_array($arg) ? '[...]' : (is_object($arg) ? '{...}' : $arg);
                }
                $t = isset($v['class']) ? $v['class'] : '';
                $t .= isset($v['type']) ? $v['type'] : '';
                $t .= isset($v['function']) ? $v['function'].'('.implode(', ', $params).')' : '';
                $t .= isset($v['file']) ? ' ['.$v['file'].(isset($v['line']) ? ':'.$v['line'] : '').']' : '';
                $traces[$k] = $t;
            }
            return implode(PHP_EOL, $traces);
        };
        if(0 == strcasecmp($trace['function'], 'ExceptionHandler')) {
            $exception = $trace['args'][0];
            return $MakeTraceString($exception->getTrace());
        } elseif(0 == strcasecmp($trace['function'], 'ErrorHandler')) {
            return $MakeTraceString($traces);
        } elseif($trace['function'] == 'FatalHandler') {
            return '';
        }
    }
}
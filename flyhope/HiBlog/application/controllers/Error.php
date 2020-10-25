<?php
/**
 * 异常处理方法
 * 
 * @author chengxuan
 */
class ErrorController extends AbsController {
    
    /**
     * 是否需要登录（默认需要）
     *
     * @var Boolean
     */
    protected $_need_login = false;

    /**
     * 错误控制入口
     * 
     * @param Exception $exception
     * @return boolean
     */
    public function errorAction(Exception $exception) {
        
        //判断当前请求是否是AJAX
        $request = Yaf_Dispatcher::getInstance()->getRequest();
        $is_ajsx = $request->isXmlHttpRequest();
        
        $is_ajsx ? $this->_ajax($exception) : $this->_html($exception);

        return false;
    }
    
    /**
     * 以网页形式处理异常
     * 
     * @param Exception $exception
     */
    protected function _html(Exception $exception) {
        if($exception instanceof ErrorException) {
            //系统错误
            if(!\Comm\Misc::isProEnv()) {
                $this->_debugHtml($exception);
            }
        } elseif($exception instanceof \Exception\Nologin) {
            //用户未登录
            header('Location:' . Comm\View::path('user/github/login'));
        } elseif($exception instanceof \Exception\Program) {
            //程序错误
            $this->viewDisplay(array(
                'exception' => $exception,
            ), 'error/error');
        } else {
            //其它异常
            $this->viewDisplay(array(
                'exception' => $exception,
            ), 'error/error');
        }
    }
    
    /**
     * 以AJAX形式处理异常
     * 
     * @param Exception $exception
     */
    protected function _ajax(Exception $exception) {
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        $data = array();
        
        if($exception instanceof ErrorException) {
            //系统错误
        } elseif($exception instanceof \Exception\Nologin) {
            //用户未登录
        } elseif($exception instanceof \Exception\Program) {
            //程序错误
        } else {
            //其它异常
        }
        
        $data = $this->_appendDebugJson($data, $exception);
        \Comm\Response::json($code, $msg, $data, false);
    }
    
    /**
     * 追加JSON高度信息
     *
     * @param array     $result    原要数据的数据
     * @param Exception $exception 异常对象
     *
     * @return array
     */
    protected function _appendDebugJson(array $result, Exception $exception) {
        if (!\Comm\Misc::isProEnv()) {
            $result['_debug']['code'] = $exception->getCode();
            $result['_debug']['message'] = $exception->getMessage();
            $result['_debug']['file'] = $exception->getFile();
            $result['_debug']['line'] = $exception->getLine();
            $result['_debug']['trace'] = $exception->getTraceAsString();
            if (method_exists($exception, 'getMetadata')) {
                $result['_debug']['metadata'] = $exception->getMetadata();
            }
        }
        return $result;
    }
    
    /**
     * 显示调试HTML
     * @param Exception $exception
     * @return boolean
     */
    protected function _debugHtml($exception) {
        try {
            $type = get_class($exception);
            $code = $exception->getCode();
            $message = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
    
            $trace = $exception->getTrace();
    
            $this->getView()->assign(array(
                'type' => $type,
                'code' => $code,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
            ));
            $this->display('debug');
        } catch (Exception $exception) {
            var_dump($exception);
        }
    }
}

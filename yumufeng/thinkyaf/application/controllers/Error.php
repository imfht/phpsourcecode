<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends Controller
{
    /**
     * 此时可通过$request->getException()获取到发生的异常
     */
    public function errorAction($exception)
    {
        switch ($exception->getCode()) {
            case YAF_ERR_AUTOLOAD_FAILED:
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
                header('HTTP/1.0 404 Not Found');
                break;
            default:
                header("HTTP/1.0 500 Internal Server Error");
                break;
        }
        $this->getView()->e = $exception;
        $this->getView()->e_class = get_class($exception);
        $params = $this->getRequest()->getParams();
        unset($params['exception']);
        $this->getView()->params = array_merge(
            array(),
            $params,
            $this->getRequest()->getPost(),
            $this->getRequest()->getQuery()
        );
    }
}
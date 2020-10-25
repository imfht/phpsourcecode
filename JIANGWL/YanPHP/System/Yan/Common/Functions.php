<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

use Yan\Core\Log;
use Yan\Core\ReturnCode;
use Yan\Core\Exception;
use Yan\Core\Config;

if (!function_exists('isCli')) {
    /**
     * 判断是否为cli访问
     *
     * @return bool
     */
    function isCli()
    {
        return defined('STDIN') || PHP_SAPI === 'cli' ? true : false;
    }
}

if (!function_exists('setHeader')) {
    /**
     * @param int $code
     */
    function setHeader($code = 200)
    {
        if (isCli()) return;
        $code = intval($code);
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Sys',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        if (!isset($status[$code])) {
            throwErr('Invalid error code', 500, Exception\InvalidArgumentException::class);
        }

        if (strpos(PHP_SAPI, 'Cgi') === 0) {
            header('Status:' . $code . ' ' . $status[$code], true);
        } else {

            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($protocol . ' ' . $code . ' ' . $status[$code], true, $code);
        }
    }
}


if (!function_exists('errorHandler')) {
    function errorHandler($severity, $errMsg, $errFile, $errLine, $errContext)
    {
        $is_error = (((E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

        if (($severity & error_reporting()) !== $severity) return;

        Log::error($errMsg, ['file' => $errFile, 'line' => $errLine, 'msg' => $errMsg]);

        /**
         * 判断是否为致命错误
         */
        if ($is_error) {
            $namespace = Config::get('namespace') ?: '\\Yan\\Core';
            $namespace .= '\\Compo\\Result';
            $result = new $namespace(ReturnCode::SYSTEM_ERROR, $errMsg);
            showResult($result);
        }

    }
}

if (!function_exists('exceptionHandler')) {
    /**
     * 异常处理
     * @param \Exception $exception
     */
    function exceptionHandler($exception)
    {
        //TODO handle db exception
        $code = $exception->getCode() == 0 || !is_numeric($exception->getCode()) ? ReturnCode::SYSTEM_ERROR : $exception->getCode();
        $code = intval($code);

        \Yan\Core\Log::error($exception->getMessage(), $exception->getTrace());
        $namespace = Config::get('namespace') ?: '\\Yan\\Core';
        $namespace .= '\\Compo\\Result';
        $result = new $namespace($code, $exception->getMessage());
        showResult($result);
    }
}

if (!function_exists('showResult')) {
    /**
     * 打印输出结果
     * @param \Yan\Core\Compo\ResultInterface $result
     */
    function showResult(\Yan\Core\Compo\ResultInterface $result)
    {
        Log::info('response=' . $result->getMessage(), $result->jsonSerialize());
        exit(json_encode($result->jsonSerialize()));
    }
}

if (!function_exists('throwErr')) {
    function throwErr(string $message = '', int $code, $exceptionClass = '\\Exception')
    {
        /** @var \Exception $exception */
        $exception = new $exceptionClass($message, $code);

        throw $exception;
    }
}

if (!function_exists('isPHP')) {
    /**
     * Determines if the current version of PHP is equal to or greater than the supplied value
     *
     * @param    string
     * @return    bool    TRUE if the current version is $version or higher
     */
    function isPHP($version)
    {
        static $_isPHP;
        $version = (string)$version;

        if (!isset($_isPHP[$version])) {
            $_isPHP[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $_isPHP[$version];
    }
}


if (!function_exists('input')) {
    /**
     * get input params
     * @param string $key format:get.a(return Input::get('a')) post.b(return Input::post('b')) c(return Input::input('c'))
     * @return array|null|string
     */
    function input($key = '')
    {
        $key = $key ?: '';

        return Yan\Core\Input::get($key);

    }
}

if (!function_exists('genResult')) {
    function genResult(int $code, string $msg, array $data = []): \Yan\Core\Compo\ResultInterface
    {
        $namespace = Config::get('namespace') ?: '\\Yan\\Core';
        $namespace .= '\\Compo\\Result';
        return new $namespace($code, $msg, $data);
    }
}

if (!function_exists('show404')) {
    function show404()
    {
        $code = ReturnCode::REQUEST_404;
        $msg = '404 Not Found';
        header("HTTP/1.1 404 Not Found");
        $result = genResult($code, $msg);
        showResult($result);
    }
}

if (!function_exists('getClassName')) {
    /**
     * 获取短类名
     *
     * @param object|string $classNameWithNamespace
     * @return bool|string
     */
    function getClassName($classNameWithNamespace)
    {
        if (is_object($classNameWithNamespace)) {
            return substr(strrchr(get_class($classNameWithNamespace), '\\'), 1);
        } elseif (is_string($classNameWithNamespace)) {
            return substr(strrchr($classNameWithNamespace, '\\'), 1);
        }
        return $classNameWithNamespace;
    }

}
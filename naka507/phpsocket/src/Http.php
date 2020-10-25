<?php
namespace Naka507\Socket;

/**
 * http protocol
 */
class Http
{

    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');

    public static $codes = array(
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
        306 => '(Unused)',
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
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    public static $instance             = null;
    public static $header               = array();
    public static $gzip                 = false;
    public static $sessionPath          = '';
    public static $sessionName          = '';
    public static $sessionGcProbability = 1;
    public static $sessionGcDivisor     = 1000;
    public static $sessionGcMaxLifeTime = 1440;
    public $sessionStarted = false;
    public $sessionFile = '';

    public static function init()
    {
        if (!self::$sessionName) {
            self::$sessionName = ini_get('session.name');
        }

        if (!self::$sessionPath) {
            self::$sessionPath = @session_save_path();
        }

        if (!self::$sessionPath || strpos(self::$sessionPath, 'tcp://') === 0) {
            self::$sessionPath = sys_get_temp_dir();
        }

        if ($gc_probability = ini_get('session.gc_probability')) {
            self::$sessionGcProbability = $gc_probability;
        }

        if ($gc_divisor = ini_get('session.gc_divisor')) {
            self::$sessionGcDivisor = $gc_divisor;
        }

        if ($gc_max_life_time = ini_get('session.gc_maxlifetime')) {
            self::$sessionGcMaxLifeTime = $gc_max_life_time;
        }
    }

    public static function input($recv_buffer, Connection $connection)
    {
        if (!strpos($recv_buffer, "\r\n\r\n")) {
            // Judge whether the package length exceeds the limit.
            if (strlen($recv_buffer) >= $connection->maxPackageSize) {
                $connection->close();
                return 0;
            }
            return 0;
        }

        list($header,) = explode("\r\n\r\n", $recv_buffer, 2);
        $method = substr($header, 0, strpos($header, ' '));

        if(in_array($method, static::$methods)) {
            return static::getRequestSize($header, $method);
        }else{
            $connection->send("HTTP/1.1 400 Bad Request\r\n\r\n", true);
            return 0;
        }
    }

    /**
      * Get whole size of the request
      * includes the request headers and request body.
      * @param string $header The request headers
      * @param string $method The request method
      * @return integer
      */
    protected static function getRequestSize($header, $method)
    {
        if($method === 'GET' || $method === 'OPTIONS' || $method === 'HEAD') {
            return strlen($header) + 4;
        }
        $match = array();
        if (preg_match("/\r\nContent-Length: ?(\d+)/i", $header, $match)) {
            $content_length = isset($match[1]) ? $match[1] : 0;
            return $content_length + strlen($header) + 4;
        }
        return $method === 'DELETE' ? strlen($header) + 4 : 0;
    }

    public static function header($content, $replace = true, $http_response_code = 0)
    {
        if (PHP_SAPI != 'cli') {
            return $http_response_code ? header($content, $replace, $http_response_code) : header($content, $replace);
        }
        if (strpos($content, 'HTTP') === 0) {
            $key = 'Http-Code';
        } else {
            $key = strstr($content, ":", true);
            if (empty($key)) {
                return false;
            }
        }

        if ('location' === strtolower($key) && !$http_response_code) {
            return self::header($content, true, 302);
        }

        if (isset(Http::$codes[$http_response_code])) {
            Http::$header['Http-Code'] = "HTTP/1.1 $http_response_code " . Http::$codes[$http_response_code];
            if ($key === 'Http-Code') {
                return true;
            }
        }

        if ($key === 'Set-Cookie') {
            Http::$header[$key][] = $content;
        } else {
            Http::$header[$key] = $content;
        }

        return true;
    }

    public static function headerRemove($name)
    {
        if (PHP_SAPI != 'cli') {
            header_remove($name);
            return;
        }
        unset(Http::$header[$name]);
    }

    public static function setcookie(
        $name,
        $value = '',
        $maxage = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $HTTPOnly = false
    ) {
        if (PHP_SAPI != 'cli') {
            return setcookie($name, $value, $maxage, $path, $domain, $secure, $HTTPOnly);
        }
        return self::header(
            'Set-Cookie: ' . $name . '=' . rawurlencode($value)
            . (empty($domain) ? '' : '; Domain=' . $domain)
            . (empty($maxage) ? '' : '; Max-Age=' . $maxage)
            . (empty($path) ? '' : '; Path=' . $path)
            . (!$secure ? '' : '; Secure')
            . (!$HTTPOnly ? '' : '; HttpOnly'), false);
    }

    public static function sessionCreateId()
    {
        mt_srand();
        return bin2hex(pack('d', microtime(true)) . pack('N',mt_rand(0, 2147483647)));
    }

    public static function sessionId($id = null)
    {
        if (PHP_SAPI != 'cli') {
            return $id ? session_id($id) : session_id();
        }
        if (static::sessionStarted() && Http::$instance->sessionFile) {
            return str_replace('sess_', '', basename(Http::$instance->sessionFile));
        }
        return '';
    }

    public static function sessionName($name = null)
    {
        if (PHP_SAPI != 'cli') {
            return $name ? session_name($name) : session_name();
        }
        $session_name = Http::$sessionName;
        if ($name && ! static::sessionStarted()) {
            Http::$sessionName = $name;
        }
        return $session_name;
    }

    public static function sessionSavePath($path = null)
    {
        if (PHP_SAPI != 'cli') {
            return $path ? session_save_path($path) : session_save_path();
        }
        if ($path && is_dir($path) && is_writable($path) && !static::sessionStarted()) {
            Http::$sessionPath = $path;
        }
        return Http::$sessionPath;
    }

    public static function sessionStarted()
    {
        if (!Http::$instance) return false;

        return Http::$instance->sessionStarted;
    }

    public static function sessionStart()
    {
        if (PHP_SAPI != 'cli') {
            return session_start();
        }

        self::tryGcSessions();

        if (Http::$instance->sessionStarted) {
            Server::console("already sessionStarted\n");
            return true;
        }
        Http::$instance->sessionStarted = true;
        // Generate a SID.
        if (!isset( $_COOKIE[Http::$sessionName]) || !is_file( realpath(Http::$sessionPath . '/sess_' . $_COOKIE[Http::$sessionName]) ) ) {
            // Create a unique session_id and the associated file name.
            while (true) {
                $session_id = static::sessionCreateId();
                if (!is_file($file_name = Http::$sessionPath . '/sess_' . $session_id)) break;
            }
            Http::$instance->sessionFile = $file_name;
            return self::setcookie(
                Http::$sessionName
                , $session_id
                , ini_get('session.cookie_lifetime')
                , ini_get('session.cookie_path')
                , ini_get('session.cookie_domain')
                , ini_get('session.cookie_secure')
                , ini_get('session.cookie_httponly')
            );
        }
        if (!Http::$instance->sessionFile) {
            Http::$instance->sessionFile = realpath(Http::$sessionPath . '/sess_' . $_COOKIE[Http::$sessionName]) ;
        }
        // Read session from session file.
        if (Http::$instance->sessionFile) {
            $raw = file_get_contents(Http::$instance->sessionFile);
            if ($raw) {
                $_SESSION = unserialize($raw);
            }
        }
        return true;
    }

    public static function sessionWriteClose()
    {
        if (PHP_SAPI != 'cli') {
            return session_write_close();
        }
        if (!empty(Http::$instance->sessionStarted) && !empty($_SESSION)) {
            $session_str = serialize($_SESSION);
            if ($session_str && Http::$instance->sessionFile) {
                return file_put_contents(Http::$instance->sessionFile, $session_str);
            }
        }
        return empty($_SESSION);
    }

    public static function tryGcSessions()
    {
        if (Http::$sessionGcProbability <= 0 ||
            Http::$sessionGcDivisor     <= 0 ||
            rand(1, Http::$sessionGcDivisor) > Http::$sessionGcProbability) {
            return;
        }

        $time_now = time();
        foreach(glob(Http::$sessionPath.'/ses*') as $file) {
            if(is_file($file) && $time_now - filemtime($file) > Http::$sessionGcMaxLifeTime) {
                unlink($file);
            }
        }
    }

}
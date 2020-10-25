<?php
/**
 * 获取用户的请求信息
 * Date: 2018\4\4 0004 22:17
 */

class Request
{

    protected static $instance = null;
    /**
     * 当前请求接受头
     * @var array
     */
    protected $header = [];

    /**
     * 构造
     * Request constructor.
     * @param array $options
     */
    public static function instance()
    {
        if (is_null(self::$instance)) self::$instance = new static();
        return self::$instance;
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function ip($type = 0, $adv = true)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前域名
     * @return string
     */
    public function domain()
    {
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        return $url;
    }


    /**
     * 设置或者获取当前的Header
     * @access public
     * @param string|array $name header名称
     * @param string $default 默认值
     * @return string
     */
    public function header($name = '', $default = null)
    {
        if (empty($this->header)) {
            $header = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $_SERVER;
                foreach ($server as $key => $val) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }
            $this->header = array_change_key_case($header);
        }
        if (is_array($name)) {
            return $this->header = array_merge($this->header, $name);
        }
        if ('' === $name) {
            return $this->header;
        }
        $name = str_replace('_', '-', strtolower($name));
        return isset($this->header[$name]) ? $this->header[$name] : $default;
    }

    /**
     * 获取操作系统类型
     * @return string
     */
    public function systems()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            return 'ios';
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            return 'Android';
        } else {
            return 'Windows';
        }
    }
}
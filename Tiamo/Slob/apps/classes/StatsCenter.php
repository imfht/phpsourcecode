<?php
namespace App;

class StatsCenter
{
    const SUCC = 1;
    const FAIL = 0;
    const TIME_OUT_STATUS = 4444;

    const PACK_STATS = 'NNCNNNN';

    const PORT_STATS  = 9903;
    const PORT_AOPNET = 9904;
    const PORT_LOG    = 9905;

    const MAX_LOG_SIZE  = 8192;

    protected static $log_socket;
    protected static $interface_tick = array();

    static $sc_svr_ip = '127.0.0.1';
    static $log_svr_ip = '192.168.1.102';
    //static $net_svr_ip = 'stats.chelun.com';
    static $net_svr_ip = '127.0.0.1';

    //默认值
    static $module_id = 1000238;
    static $registerShutdown = false;
    static $tick_array = array();
    static $round_index = 0;
    static $enable = true;

    static function setServerIp($ip)
    {
        self::$sc_svr_ip = $ip;
    }

    /**
     * 自动获取接口,首先获取本地缓存，如果没有则从服务器拉取
     * @param $interface_key
     * @param $module
     * @return int|string
     */
    static function getInterfaceId($interface_key, $module)
    {
        if (!self::$enable)
        {
            return 1;
        }
        $file = '/tmp/mostats/'.$module.'_'.$interface_key;
        if (!is_dir('/tmp/mostats'))
        {
            mkdir('/tmp/mostats');
        }
        if (is_file($file))
        {
            $id = file_get_contents($file);
            return $id;
        }
        else
        {
            $id=file_get_contents(\Swoole::$php->config["common"]["api_interface"]."?name=".$interface_key);
            if(is_numeric($id)){
                $new_id=$id;
            }else{
                return 0;
            }
            if ($new_id)
            {
                file_put_contents($file, $new_id);
                return $new_id;
            }
            //网络调用失败了
            else
            {
                return 0;
            }
        }
    }

    /**
     * 发送UDP数据包
     * @param $data
     * @param $port
     */
    static function _send_udp($data, $port)
    {
        if (self::$enable)
        {
            $cli = stream_socket_client('udp://' . self::$sc_svr_ip . ':' . $port, $errno, $errstr);
            stream_socket_sendto($cli, $data);
        }
    }

    static function tick($interface, $module)
    {
        if (!is_numeric($interface))
        {
            $interface = self::getInterfaceId($interface, $module);
        }
        if (!self::$registerShutdown)
        {
            register_shutdown_function('\App\StatsCenter::onShutdown');
            self::$registerShutdown = true;
        }
        $obj = new StatsCenter_Tick($interface, $module, self::$round_index);
        self::$tick_array[self::$round_index] = $obj;
        self::$round_index ++;
        return $obj;
    }

    /**
     * PHP结束时发送所有统计请求
     */
    static function onShutdown()
    {
        /**
         * @var $tick StatsCenter_Tick
         */
        foreach(self::$tick_array as $tick)
        {
            $tick->report(false, 4444);
        }
        StatsCenter_Tick::sendPackage();
    }

    /**
     * 发送日志信息，最大不得超过8K
     * @param $level
     * @param $type
     * @param $subtype
     * @param $msg
     * @param $uid
     * @return bool
     */
    static function log($level, $type, $subtype, $msg, $uid = 0)
    {
        if (!self::$log_socket)
        {
            self::$log_socket = stream_socket_client('tcp://' . self::$log_svr_ip . ':' . self::PORT_LOG, $errno, $errstr, 1,
                STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT);
            if (empty(self::$log_socket))
            {
                return false;
            }
        }

        if (strlen($msg) > self::MAX_LOG_SIZE)
        {
            trigger_error("log message max size is " . self::MAX_LOG_SIZE);
            return false;
        }

        $log = self::$module_id . "|$level|$type|$subtype|$uid\n$msg\r\n";
        $length = strlen($log);

        for ($written = 0; $written < $length; $written += $fwrite)
        {
            $fwrite = fwrite(self::$log_socket, substr($log, $written));
            if ($fwrite === false)
            {
                return false;
            }
        }
        return true;
    }
}

class StatsCenter_Tick
{
    protected $interface;
    protected $module_id;
    protected $start_ms;
    protected $params;
    protected $id;
    protected $_end = false;

    const STATS_PKG_LEN = 25;
    const STATS_PKG_NUM = 58;

    protected static $_send_udp_pkg = '';
    protected $_time_out_pkg = array();

    function __construct($interface, $module, $id)
    {
        $this->interface = $interface;
        $this->module = $module;
        $this->start_ms = microtime(true);
        $this->_time_out_pkg = array(
            'interface' => $this->interface,
            'module' => $this->module,
            'success' => StatsCenter::FAIL,
            'ret_code' => StatsCenter::TIME_OUT_STATUS,
            'server_ip' => 0,
            'use_time' => 0,
            'time' => 0
        );
        $this->id = $id;
    }

    function addParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    function report($success, $ret_code = 0, $server_ip = 0)
    {
        //避免重复调用
        if ($this->_end)
        {
            return;
        }
        $this->_time_out_pkg = array();
        $use_ms = intval((microtime(true) - $this->start_ms) * 1000);
        $pkg = pack(StatsCenter::PACK_STATS,
            $this->interface,
            $this->module,
            $success,
            $ret_code,
            ip2long($server_ip),
            $use_ms, time());

        self::$_send_udp_pkg .= $pkg;

        //60个统计时发送数据包，避免超过最大传输单元，1500 MTU
        if (strlen(self::$_send_udp_pkg) >= self::STATS_PKG_LEN * self::STATS_PKG_NUM)
        {
            self::sendPackage();
        }
        //关闭上报
        $this->_end = true;
        unset(StatsCenter::$tick_array[$this->id]);
    }

    function reportSucc($success,$server_ip)
    {
        $this->report($success, 0, $server_ip);
    }

    function reportCode($ret_cod,$server_ip)
    {
        if ($ret_cod === 0)
        {
            $this->report(StatsCenter::SUCC, $ret_cod, $server_ip);
        }
        else
        {
            $this->report(StatsCenter::FAIL, $ret_cod, $server_ip);
        }
    }

    static function sendPackage()
    {
        StatsCenter::_send_udp(self::$_send_udp_pkg, StatsCenter::PORT_STATS);
        self::$_send_udp_pkg = '';
    }

    static function getIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        else
        {
            if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            }
            else
            {
                if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                {
                    $ip = getenv("REMOTE_ADDR");
                }
                else
                {
                    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    else
                    {
                        $ip = "0.0.0.0";
                    }
                }
            }
        }
        return $ip;
    }
}

<?php
/**
 * Monitor 上报，需要配合Waf使用
 *
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2019
 * @version		$Id$
 * @package		com.tools
 * @since		v1.1
 */

class WMonitorClient
{
    /**
     * [module=>[interface=>time_start, interface=>time_start ...], module=>[interface=>time_start ..], ... ]
     * @var array
     */
    protected static $timeMap	= array();
	protected static $_conf		= array('ip' => '127.0.0.1', 'port' => '50100');

    /**
     * 模块接口上报消耗时间记时
     * @param string $module
     * @param string $interface
     * @return void
     */
    public static function tick($server_name = '', $module = '', $interface = '')
    {
        self::$timeMap[$server_name][$module][$interface] = self::getCurrentMsectime();
    }

    /**
     * 上报统计数据
     * @param string $module
     * @param string $interface
     * @param bool $success
     */
    public static function report($server_name = '', $module = '', $interface = '', $success = true)
    {
        if(isset(self::$timeMap[$server_name][$module][$interface]) && self::$timeMap[$server_name][$module][$interface] > 0)
        {
            $time_start = self::$timeMap[$server_name][$module][$interface];
            self::$timeMap[$server_name][$module][$interface] = 0;  // 记时复位
        }else{
            $time_start = self::getCurrentMsectime();
        }

        $cost_time = self::getCurrentMsectime() - $time_start;

        $arrParams = array(
            'url'               =>  '/monitor/interfaceStatistics',
            'server_name'       =>  $server_name,
            'module_name'       =>  $module,
            'interface_name'    =>  $interface,
            'cost_time'         =>  $cost_time,
            'success'           =>  $success,
        );

        $conf   = self::$_conf;

        self::sendNormalUdpData($conf, $arrParams);
    }

    /**
     * 上报累计数据
     * @param string $module
     * @param string $interface
     * @param bool $success
     */
    public static function coutReport($server_name = '', $module = '', $interface = '', $count = 1)
    {
        $arrParams = array(
            'url'               =>  '/monitor/countStatistics',
            'server_name'       =>  $server_name,
            'module_name'       =>  $module,
            'interface_name'    =>  $interface,
            'count'             =>  $count,
        );

        $conf   = self::$_conf;
        self::sendNormalUdpData($conf, $arrParams);
    }

    /**
     * 获取当前时间戳的毫秒数
     *
     * @return float
     */
    public static function getCurrentMsectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * 获取本Worker进程的全局seq
     *
     * @return int
     */
    protected static function getWorkerGlobalSeq()
    {
        $intRet = 0;
        return $intRet;
    }

    /**
     * 转换成rpc的参数形式
     *
     * @param array $arrParams
     * @return string
     */
    protected static function getRpcParams($arrParams = array())
    {
        $strRet = '';

        $arrParams['seq']   = self::getWorkerGlobalSeq();
        $strRet = json_encode($arrParams, JSON_UNESCAPED_UNICODE);

        return $strRet;
    }

    /**
     * 发送正常udp包
     *
     * @param $conf
     * @param $arrParams
     * @return bool
     */
    public static function sendNormalUdpData($conf, $arrParams)
    {
        $socket = stream_socket_client("udp://{$conf['ip']}:{$conf['port']}", $errno, $errstr);
        if(!$socket)
        {
            return false;
        }
        $buffer = self::getRpcParams($arrParams);
        return stream_socket_sendto($socket, $buffer) == strlen($buffer);
    }
}
<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

use tfc\ap\ErrorException;
use tfc\util\Ral;

/**
 * RalProxy class file
 * Ral代理操作类，调用CURL失败重试、记录操作日志、负载均衡管理（待扩展）
 *
 * 配置 /cfg/ral/cluster.php：
 * <pre>
 * return array (
 *   'administrator' => array (
 *     'server' => string,               // 服务器IP地址
 *     'port' => integer,                // 服务器端口号
 *     'connect_time_out_ms' => integer, // 链接超时：毫秒
 *     'time_out_ms' => integer,         // 执行超时：毫秒
 *     'converter' => string,            // 执行后返回数据类型
 *     'retry' => integer,               // 重试次数
 *   ),
 *   'site' => array (
 *     'server' => '127.0.0.1',
 *     'port' => 80,
 *     'connect_time_out_ms' => 200,
 *     'time_out_ms' => 500,
 *     'converter' => 'json',
 *     'retry' => 1,
 *   ),
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: RalProxy.php 1 2013-04-05 01:38:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class RalProxy
{
    /**
     * @var integer 执行失败后，尝试重试的最大次数
     */
    const MAX_RETRY_TIMES = 3;

    /**
     * @var string 寄存Ral配置名
     */
    protected $_clusterName = null;

    /**
     * @var array 寄存Ral配置
     */
    protected $_config = null;

    /**
     * @var instance of tfc\util\Ral
     */
    protected $_ral = null;

    /**
     * 构造方法：初始化Ral配置名
     * @param string $clusterName
     */
    public function __construct($clusterName)
    {
        $this->_clusterName = $clusterName;
    }

    /**
     * CURL方式提交数据
     * @param string $pathinfo
     * @param array $params
     * @param string $method
     * @return mixed
     */
    public function talk($pathinfo, array $params = array(), $method = 'GET')
    {
        $ral = $this->getRal();
        $ral->setLogId(Log::getId());

        $maxRetry = $this->getRetry();
        for ($retry = 0; $retry < $maxRetry; $retry++) {
            try {
                $result = $ral->talk($pathinfo);
                $message = 'Ral Exec Curl Successfully!';
                $code = 0;
            }
            catch (ErrorException $e) {
                $message = 'Ral Exec Curl Failed! ' . $e->getMessage();
                $code = $e->getCode();
                $result = false;
            }

            $event = array(
                'msg' => $message,
                'retry' => $retry,
                'pathinfo' => $pathinfo,
                'params' => serialize($params),
                'method' => $method,
                'config' => serialize($this->getConfig())
            );

            if ($result !== false) {
                Log::notice($event, __METHOD__);
                return $result;
            }

            Log::warning($event, $code, __METHOD__);
        }

        return $result;
    }

    /**
     * 获取Ral对象
     * @return \tfc\util\Ral
     */
    public function getRal()
    {
        if ($this->_ral === null) {
            $this->_ral = new Ral($this->getServer(), $this->getPort(), $this->getConnectTimeOutMs(), $this->getTimeOutMs(), $this->getConverter());
        }

        return $this->_ral;
    }

    /**
     * 获取Ral服务名
     * @return string
     */
    public function getServer()
    {
        return $this->getConfig('server');
    }

    /**
     * 获取Ral端口号
     * @return integer
     */
    public function getPort()
    {
        return $this->getConfig('port');
    }

    /**
     * 获取Ral链接超时：毫秒
     * @return integer
     */
    public function getConnectTimeOutMs()
    {
        return $this->getConfig('connect_time_out_ms');
    }

    /**
     * 获取Ral执行超时：毫秒
     * @return integer
     */
    public function getTimeOutMs()
    {
        return $this->getConfig('time_out_ms');
    }

    /**
     * 获取Ral执行后返回数据类型
     * @return string
     */
    public function getConverter()
    {
        return $this->getConfig('converter');
    }

    /**
     * 获取调用CURL失败重试次数
     * @return integer
     */
    public function getRetry()
    {
        return $this->getConfig('retry');
    }

    /**
     * 获取Ral配置信息，如果配置信息中没有指定连接服务器失败尝试重连次数，则由MAX_RETRY_TIMES常量指定次数
     * @param mixed $key
     * @return mixed
     * @throws ErrorException 如果没有指定服务器名称或IP地址、服务器端口号、连接超时、执行超时或获取数据后转码方式，抛出异常
     */
    public function getConfig($key = null)
    {
        if ($this->_config === null) {
            $config = Cfg::getRal($this->getClusterName());
            if (!isset($config['server']) || !isset($config['port']) || !isset($config['connect_time_out_ms']) || !isset($config['time_out_ms']) || !isset($config['converter'])) {
                throw new ErrorException(sprintf(
                    'RalProxy no entry is registered for key: server|port|connect_time_out_ms|time_out_ms|converter in ral config "%s"', serialize($config)
                ));
            }
            $config['retry'] = isset($config['retry']) ? (int) $config['retry'] : self::MAX_RETRY_TIMES;
            $this->_config = $config;
        }

        if ($key === null) {
            return $this->_config;
        }

        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    /**
     * 获取Ral配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }
}

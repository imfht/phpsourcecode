<?php

/**
 * 百度地图API
 *
 * @package Api
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Api;

class Map {
    
    /**
     * 百度地图AK
     * 
     * @var string
     */
    protected $_ak;
    
    /**
     * 百度地图SK
     * 
     * @var string
     */
    protected $_sk;
    
    /**
     * 百度地图基础API
     * 
     * @var string
     */
    protected $_base = 'http://api.map.baidu.com';
    
    
    /**
     * 构造方法
     * 
     * @param string $ak
     * @param string $sk
     * 
     * @return void
     */
    public function __construct($ak = null, $sk = null) {
        if(!$ak || !$sk) {
            $config = new \Yaf_Config_Ini(CONF_PATH . 'env.ini');
            $ak = $config->baidu->map->ak;
            $sk = $config->baidu->map->sk;
        }
        
        $this->_ak = $ak;
        $this->_sk = $sk;
    }
    
    /**
     * 根据IP获取位置
     * 
     * @param string $ip
     * @param string $coor
     */
    public function locationIp($ip = '', $coor = 'bd09ll') {
        //内网IP直接按照北京处理
        if($ip == '127.0.0.1') {
            $ip = '202.106.0.20';
        }
        
        $params = array(
            'ip'   => $ip,
            'coor' => $coor,
        );
        return $this->_fetch('/location/ip', $params);
    }
    
    /**
     * 获取数据
     * 
     * @param string $api
     * @param array  $params
     */
    protected function _fetch($api, array $params = array()) {
        
        //调用sn计算函数，默认get请求
        $params['ak'] = $this->_ak;
        $sn = $this->_caculateAKSN($api, $params);
        $params['sn'] = $sn;
        
        $url = "{$this->_base}{$api}?" . http_build_query($params);
        
        $request = new \Comm\Request\Single($url);
        $result = $request->setTimeout(5)->exec();
        $result = json_decode($result);
        return $result;
    }
    
    /**
     * 计算SN
     * 
     * @param string $url
     * @param array  $querystring_arrays
     * @param string $method
     * 
     * @return string
     */
    protected function _caculateAKSN( $url, $querystring_arrays, $method = 'GET') {
        if($method === 'POST') {
            ksort($querystring_arrays);
        }
        $querystring = http_build_query($querystring_arrays);
        return md5(urlencode($url . '?' . $querystring . $this->_sk));
    }
    
}
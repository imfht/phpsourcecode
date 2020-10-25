<?php

/**
 * HTTP批量请求
 *
 * @author    Chengxuan <i@chengxuan.li>
 * @package   Comm
 */
namespace Comm\Request;
class Multi {

    /**
     * 批量请求CURL的资源
     *
     * @var resource
     */
    protected $_mh;

    /**
     * 批量的CURL请求
     *
     * @var array
     */
    protected $_curls = array();

    /**
     * 构造方法
     *
     * @return void
     */
    public function __construct() {
        $this->_mh = curl_multi_init();
    }

    /**
     * 析构方法
     *
     * @return void
     */
    public function __destruct() {
        curl_multi_close($this->_mh);
    }

    /**
     * 添加Request_Single请求
     *
     * @param string                $k       KEY名
     * @param \Comm\Request\Single $request Helper_Request_Single请求
     *
     * @return \Comm\Request\Multi
     */
    public function addRequest($k, Single $request) {
        return $this->addCurl($k, $request->fetchCurl());
    }

    /**
     * 添加CURL请求
     *
     * @param string   $k  KEY名
     * @param resource $ch CURL资源
     *
     * @return \Comm\Request\Multi
     */
    public function addCurl($k, $ch) {
        //之前存在KEY，直接报错
        if(isset($this->_curls[$k])) {
            throw new \Exception\Program('key exists', null, array(
                'curls'       => $this->_curls,
                'current_key' => $k,
            ));
        }

        $this->_curls[$k] = $ch;
        curl_multi_add_handle($this->_mh, $ch);
        return $this;
    }

    /**
     * 处理请求，返回数据
     *
     * @return array
     */
    public function exec() {
        //批量处理请求
        do {
            $mrc = curl_multi_exec($this->_mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->_mh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->_mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        //获取数据
        $result = array();
        foreach($this->_curls as $key=>$ch) {
            $result[$key] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($this->_mh, $ch);
        }
        return $result;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2018/3/16
 * Time: 12:20
 * Email:liyongsheng@meicai.cn
 */
class CurlMultiException extends Exception{}
class CurlMulti
{
    public $handle;
    private $_subHandles= [];

    public function __construct()
    {
        $this->handle = curl_multi_init();
    }

    /**
     * 一次设置多个handle
     * @param array $subHandles
     * @return $this
     */
    public function setHandles(array $subHandles){
        foreach($subHandles as $key=>$subHandle){
            $this->addHandle($key, $subHandle);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param resource $subHandle
     * @return $this
     * @throws CurlMultiException
     */
    public function addHandle($key, $subHandle)
    {
        if(is_resource($subHandle)==false){
            throw new CurlMultiException('subHandle 必须为curl_init返回的资源');
        }
        $this->_subHandles[$key] = $subHandle;
        return $this;
    }

    /**
     * 执行 curl_multi
     * @param bool $json2Array
     * @return array
     * @throws CurlMultiException
     */
    public function run($json2Array=true){
        if(count($this->_subHandles)<1){
            throw new CurlMultiException('没有需要执行的curl请求');
        }
        foreach($this->_subHandles as $subHandle) {
            curl_multi_add_handle($this->handle, $subHandle);
        }
        $active = null;
        do {
            $status = curl_multi_exec($this->handle, $active);
            if($status > 0) {
                throw new CurlMultiException(curl_multi_strerror($status));
            }
        } while ($status == CURLM_CALL_MULTI_PERFORM  || $active);
        $response = [];
        foreach ($this->_subHandles as $key => $ch) {
            if($errorNo = curl_errno($ch)) {
                throw new CurlMultiException(curl_error($ch), $errorNo);
            }
            $info = curl_getinfo($ch);
            if($info['http_code']<200 || $info['http_code']>=400){
                throw new CurlMultiException('调用api接口失败http_code是'.$info['http_code'].' request url '.$info['url']);
            }
            if($json2Array){
                $response[$key] = json_decode(curl_multi_getcontent($ch), 1);
            }else {
                $response[$key] = curl_multi_getcontent($ch); // get the content
            }
        }
        return $response;
    }

    public function __destruct()
    {
        if(is_resource($this->handle)) {
            foreach ($this->_subHandles as $subHandle) {
                curl_multi_remove_handle($this->handle, $subHandle);
            }
            curl_multi_close($this->handle);
        }
    }
}
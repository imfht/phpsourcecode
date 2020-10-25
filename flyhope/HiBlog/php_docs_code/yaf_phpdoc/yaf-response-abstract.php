<?php
/**
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
class Yaf_Response_Abstract {

    const DEFAULT_BODY = null;

    protected $_header;

    protected $_body;

    protected $_sendheader;

    /**
     * send response
     *
     * @return void
     */
    public function response() {}

    /**
     * 设置响应的Body
     *
     * @param string $content 
     * @param string $key body所对应的key，你可以设置一个body的键值对，如果你没有指定key，系统默认使用Yaf_Response_Abstract::DEFAULT_BODY this parameter is introduced as of 2.2.0
     *
     * @return bool
     */
    public function setBody($content, $key = null) {}

    /**
     * 清除已经设置的响应body
     *
     * @param string $key the content key, if you don't specific, then all contents will be cleared. 如果你没选择具体清除哪个key所对应的内容，那所有内容将被清除 this parameter is introduced as of 2.2.0
     *
     * @return bool
     */
    public function clearBody($key = null) {}

    /**
     * The __toString purpose
     *
     * @return void
     */
    private function __toString() {}

    /**
     * The prependBody purpose
     *
     * @param string $content 
     * @param string $key body所对应的key，你可以设置一个body的键值对，如果你没有指定key，系统默认使用Yaf_Response_Abstract::DEFAULT_BODY this parameter is introduced as of 2.2.0
     *
     * @return bool
     */
    public function prependBody($content, $key = null) {}

    /**
     * The __destruct purpose
     *
     * @return void
     */
    public function __destruct() {}

    /**
     * The setRedirect purpose
     *
     * @return void
     */
    public function setRedirect() {}

    /**
     * The setAllHeaders purpose
     *
     * @return void
     */
    protected function setAllHeaders() {}

    /**
     * The clearHeaders purpose
     *
     * @return void
     */
    public function clearHeaders() {}

    /**
     * The setHeader purpose
     *
     * @return void
     */
    public function setHeader() {}

    /**
     * 获取已经设置的响应body
     *
     * @param string $key body所对应的key，如果你没指定key，系统默认使用Yaf_Response_Abstract::DEFAULT_BODY。如果你传入一个NULL，所有的内容将会以数组形式被返回。 this parameter is introduced as of 2.2.0
     *
     * @return mixed
     */
    public function getBody($key = null) {}

    /**
     * The __clone purpose
     *
     * @return void
     */
    private function __clone() {}

    /**
     * 往已有的响应body后附加新的内容
     *
     * @param string $content 
     * @param string $key 响应内容的key，你可以设置一个键值对，如果你没有具体的设置的话，系统默认使用Yaf_Response_Abstract::DEFAULT_BODY this parameter is introduced as of 2.2.0
     *
     * @return bool
     */
    public function appendBody($content, $key = null) {}

    /**
     * The __construct purpose
     */
    public function __construct() {}

    /**
     * The getHeader purpose
     *
     * @return void
     */
    public function getHeader() {}


}
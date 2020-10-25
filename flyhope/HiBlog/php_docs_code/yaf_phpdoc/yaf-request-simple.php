<?php
/**
 * Yaf_Request_Simple 特别的被用于测试。例如：CLI模式下模拟一些特殊的要求
 * 
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
class Yaf_Request_Simple extends Yaf_Request_Abstract {

    const SCHEME_HTTP = null;

    const SCHEME_HTTPS = null;

    /**
     * The getQuery purpose
     *
     * @return void
     */
    public function getQuery() {}

    /**
     * The getPost purpose
     *
     * @return void
     */
    public function getPost() {}

    /**
     * The getFiles purpose
     *
     * @return void
     */
    public function getFiles() {}

    /**
     * The isXmlHttpRequest purpose
     *
     * @return void
     */
    public function isXmlHttpRequest() {}

    /**
     * The getCookie purpose
     *
     * @return void
     */
    public function getCookie() {}

    /**
     * The __clone purpose
     *
     * @return void
     */
    private function __clone() {}

    /**
     * The getRequest purpose
     *
     * @return void
     */
    public function getRequest() {}

    /**
     * The __construct purpose
     */
    function __construct() {}

    /**
     * The get purpose
     *
     * @return void
     */
    public function get() {}


}
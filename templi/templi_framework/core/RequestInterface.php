<?php

/**
 * RequestInterface.php
 * @author: liyongsheng
 * @email： liyongsheng@huimai365.com
 * @date: 2015/6/10
*/

namespace framework\core;


interface RequestInterface
{
    /**
     * 获取post 值
     * @param $key string
     * @param mixed $default
     * @return mixed
     */
    public function post($key=null, $default=null);

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($key=null, $default=null);

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function put($key=null, $default=null);

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function delete($key=null, $default=null);

    /**
     * 获取 http body 部分
     * @return mixed
     */
    public function getRawBody();

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getBodyParam($key=null, $default=null);
}
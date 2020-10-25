<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\response;

/**
 * Native
 *
 * @package nb\response
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Php extends Driver {

    /**
     * 设置http头
     * @param $key
     * @param null $value
     * @param null $http_response_code
     */
    public function header($key, $value=null,$http_response_code=null) {
        if($value) {
            $key = $key.':'.$value;
        }
        headers_sent() or header($key,true,$http_response_code);
    }

    /**
     * 设置HTTP状态
     *
     * @access public
     * @param integer $code http代码
     * @return void
     */
    public function status($code) {
        if (isset($this->_httpCode[$code])) {
            header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') . ' ' . $code . ' ' . $this->_httpCode[$code], true, $code);
        }
    }

    /**
     * 在http头部请求中声明类型和字符集
     *
     * @access public
     * @param string $contentType 文档类型
     * @return void
     */
    public function contentType($contentType = 'html',$charset = 'UTF-8') {
        header('Content-Type: ' . $this->_mimeType[$contentType] . '; charset=' . $charset, true);
    }

    /**
     * 返回来路
     *
     * @access public
     * @param string $suffix 附加地址
     * @param string $default 默认来路
     */
    public function goBack($suffix = NULL, $default = NULL) {
        //获取来源
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        //判断来源
        if ($referer) {
            // ~ fix Issue 38
            if ($suffix) {
                $parts = parse_url($referer);
                $myParts = parse_url($suffix);

                if (isset($myParts['fragment'])) {
                    $parts['fragment'] = $myParts['fragment'];
                }

                if (isset($myParts['query'])) {
                    $args = [];
                    if (isset($parts['query'])) {
                        parse_str($parts['query'], $args);
                    }

                    parse_str($myParts['query'], $currentArgs);
                    $args = array_merge($args, $currentArgs);
                    $parts['query'] = http_build_query($args);
                }
                $referer = $this->buildUrl($parts);
            }
            redirect($referer, false);
        }

        if ($default) {
            redirect($default);
        }
        exit;
    }

}
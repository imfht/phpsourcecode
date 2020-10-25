<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * HttpResponse class file
 * HTTP响应模式发送类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: HttpResponse.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class HttpResponse extends Response
{
    /**
     * 设置ContentType信息及页面编码
     * @param string $contentType
     * @param string $charset
     * @return void
     */
    public function contentType($contentType = 'text/html', $charset = 'utf-8')
    {
        $this->headersSent(true);
        $contentType = 'Content-Type: ' . $contentType . '; charset=' . $charset;
        header($contentType, true);
    }

    /**
     * 页面重定向，如果header未发送，header方式重定向，否则用meta http-equiv="refresh"方式
     * @param string $url
     * @param string $message
     * @param integer $delay
     * @param integer $statusCode
     * @return void
     */
    public function redirect($url, $message = '', $delay = 0, $statusCode = 302)
    {
        if ($this->headersSent()) {
            $this->metaRefresh($url, $message, $delay);
        }
        else {
            if (($delay = (int) $delay) > 0) {
                $this->headerRefresh($url, $message, $delay, $statusCode);
            }
            else {
                $this->location($url, $statusCode);
            }
        }
    }

    /**
     * 设置页面重定向URL，立即跳转
     * @param string $url
     * @param integer $statusCode
     * @return void
     */
    public function location($url, $statusCode = 302)
    {
        $this->headersSent(true);
        $this->setStatusCode($statusCode);
        header('Location: ' . $url, true, $this->getStatusCode());
    }

    /**
     * 设置页面重定向URL，header方式重定向，延迟一段时间后跳转
     * @param string $url
     * @param string $message
     * @param integer $delay
     * @param integer $statusCode
     * @return void
     */
    public function headerRefresh($url, $message = '', $delay = 0, $statusCode = 302)
    {
        $this->headersSent(true);
        $this->setStatusCode($statusCode);
        $delay = (int) $delay;
        $refresh = 'Refresh: ' . $delay . '; url=' . $url;
        header($refresh, true, $this->getStatusCode());
        if ($delay > 0) {
            echo $message;
        }
    }

    /**
     * 设置页面重定向URL，meta方式重定向，延迟一段时间后跳转
     * @param string $url
     * @param string $message
     * @param integer $delay
     * @return void
     */
    public function metaRefresh($url, $message = '', $delay = 0)
    {
        $delay = (int) $delay;
        $refresh = '<meta http-equiv="refresh" content="' . $delay . '; url=' . $url . '" />';
        if ($delay > 0) {
            $refresh .= $message;
        }
        echo $refresh;
    }
}

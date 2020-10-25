<?php
/**
 * Created by weibo.com
 * User: wenlong11
 * Date: 2018/9/19
 * Time: 上午11:08
 */

namespace Kernel\Swoole\Event\Http;


use Kernel\AgileCore;
use Kernel\Core\Mime\Response;

class Statics
{

    /**
     * 执行处理一个静态文件
     *
     * @param string                $request_uri
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     */
    public static function exec($request_uri, \Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

        $filename = APP_PATH . "/public{$request_uri}";
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // 发送Mime
        $comm_response = new Response($response);
        $contentType = $comm_response->contentType($extension);

        $config = AgileCore::getInstance()->getConfig('statics');



        // 浏览器缓存 && \Comm\Misc::isProEnv()
        if (isset($config['expire']) && $config['expire'] > 0) {
            $response->header('Cache-control', "max-age={$config['expire']}");
        }

        if (method_exists($response, 'gzip')) {
            $response->gzip(1);
        }

        self::_sendfile($filename, $response, $contentType);

    }

    /**
     * 发送静态文件内容
     *
     * @param string                $filename 文件内容
     * @param \Swoole\Http\Response $response Swoole响应
     * @param callable              $callback 自定义回调返回内容
     */
    protected static function _sendfile($filename, \Swoole\Http\Response $response, string $contentType, callable $callback = null)
    {
        if (is_file($filename)) {
            $response->header('Content-Type', $contentType);
            $response->sendfile($filename);
            $response->end();
//            \Swoole\Async::readFile($filename, function ($content) use ($response, $callback, $contentType) {
//                if ($callback && is_callable($callback)) {
//                    $content = call_user_func($callback, $content);
//                }
//                $response->sendfile($filename);
//                $response->header('Content-Type', $contentType);
//                $response->end($content);
//            });
        } else {
            // 页面没找到
            $response->status(404);
            $comm_response = new Response($response);
            $response->header('Content-Type', $comm_response->contentType('html'));
            $response->end('<h1>404 NOT FOUND</h1>');
        }
    }
}
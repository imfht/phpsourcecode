<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:36
 */

namespace fastwork\swoole;


use fastwork\exception\HttpRuntimeException;
use fastwork\facades\Error;
use think\exception\TemplateNotFoundException;

class HttpServer extends Server
{

    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {

        if ($request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
        };

        $this->app->request->setHttpRequest($request);
        $this->app->response->setHttpResponse($response);

        try {
            $router = $this->app->route->dispath(
                $this->app->request
            );

            foreach ($this->app->response->getHeader() as $k => $v) {
                $response->header($k, $v);
            }
            //清除缓存
            $this->app->response->clear();

            //数组或者对象，转string
            if (is_array($router) || is_object($router)) {
                $router = $this->app->response->json($router);
            }
            return $response->end($router);
        } catch (HttpRuntimeException $e) {
            $router = Error::render($this->app->response, $e);
            return $response->end($router);
        } catch (TemplateNotFoundException $e) {
            $router = Error::render($this->app->response, $e);
            return $response->end($router);
        } catch (\Throwable $e) {
            Error::report($e);
            return $response->end();
        }
    }
}
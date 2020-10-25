<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-16
 * Time: 13:44
 */

namespace inhere\gearman\jobs;

/**
 * Class StdApiProxyJob - 通用的api请求代理job handler
 *
 * - 有一些接口请求特别频繁，我们可以单独创建一个handler来转发它
 *
 * example:
 *
 * ```
 * $mgr->addHandler('refreshToken', new StdApiProxyJob('http://user.domain.com', '/refreshToken'));
 * ```
 *
 * in client:
 *
 * ```
 * $client->doBackground('refreshToken', [
 *     'userId' => 123,
 *     'token' => 'xxdd',
 *     // ... ...
 * ]);
 * ```
 *
 * @package inhere\gearman\jobs
 */
class StdApiProxyJob extends RequestProxyJob
{
    /**
     * StdApiProxyJob constructor.
     * @param string $baseUrl
     * @param string $path
     * @param string $method
     */
    public function __construct($baseUrl, $path, $method = 'GET')
    {
        $this->method = trim($method);
        $this->baseUrl = trim($baseUrl);
        $this->path = trim($path);

        parent::__construct();
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function beforeSend(array &$payload)
    {
        if (isset($payload['_method'])) {
            $this->method = trim($payload['_method']);
            unset($payload['_method']);
        }

        return true;
    }
}

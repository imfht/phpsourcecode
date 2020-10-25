<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-16
 * Time: 13:44
 */

namespace inhere\gearman\jobs;

/**
 * Class StdHostProxyJob - 通用的项目(主机/域)请求代理job handler
 *
 * - 创建handler时指定 baseUrl(通常是域名地址)
 * - 在客户端添加job时，设定要请求的 URI(通过发送参数 `_uri`), 可选的 `_method` 设置请求方法
 *
 * example:
 *
 * ```
 * $mgr->addHandler('user_api', new StdHostProxyJob('http://user.domain.com'));
 * $mgr->addHandler('goods_api', new StdHostProxyJob('http://goods.domain.com'));
 * ```
 *
 * in client:
 *
 * ```
 * $client->doBackground('user_api', [
 *     '_uri' => '/update-info', // will request: http://user.domain.com/update-info
 *     'userId' => 123,
 *     // ... ...
 * ]);
 * ```
 *
 * @package inhere\gearman\jobs
 */
class StdHostProxyJob extends RequestProxyJob
{
    /**
     * StdHostProxyJob constructor.
     * @param $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = trim($baseUrl);

        parent::__construct();
    }

    /**
     * @param array $payload
     * @return bool
     */
    protected function beforeSend(array &$payload)
    {
        if (!isset($payload['_uri']) || !$payload['_uri']) {
            return false;
        }

        if (isset($payload['_method'])) {
            $this->method = trim($payload['_method']);
            unset($payload['_method']);
        }

        $this->path = trim($payload['_uri']);

        unset($payload['_uri']);
        return true;
    }
}

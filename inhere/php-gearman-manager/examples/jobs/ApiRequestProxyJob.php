<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-16
 * Time: 10:29
 */

namespace inhere\gearman\examples\jobs;

use app\components\RequestProxyJob;

/**
 * Class ApiRequestProxyJob
 * @package inhere\gearman\examples\jobs
 */
class ApiRequestProxyJob extends RequestProxyJob
{
    /**
     * @param array $payload
     * @return bool
     */
    protected function beforeSend(array $payload)
    {
        if (!isset($payload['_uri']) || !$payload['_uri']) {
            return false;
        }

        if (isset($payload['_method'])) {
            $this->method = trim($payload['_method']);
        }

        $this->baseUrl = 'your api host'; // TODO write you api host address. eg: http://api.site.com
        $this->path = trim($payload['_uri'], $payload['_method']);

        return true;
    }
}

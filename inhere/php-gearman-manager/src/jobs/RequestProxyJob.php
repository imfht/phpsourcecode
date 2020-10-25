<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-05-11
 * Time: 16:38
 */

namespace inhere\gearman\jobs;

use inhere\gearman\tools\CurlHelper;

/**
 * Class RequestProxyJob
 *
 * - 通用的请求转发工作处理器基类.
 * - 你只需要关注数据验证，设置好正确的 `$baseUrl`(api host) 和 `$path`(api path) (可选的 `$method`)
 * - 正确的数据将会原样的发送给接口地址(`$baseUrl + $path`)
 *
 * example:
 *
 * ```php
 * class UserAfterRegisterJob extends RequestProxyJob
 * {
 *   protected function beforeSend(array &$payload)
 *   {
 *       if (!isset($payload['userId']) || $payload['userId'] <= 0) {
 *           return false;
 *       }
 *
 *       $this->baseUrl = 'http://inner-api.domain.com';
 *       $this->path = '/user/after-register';
 *
 *       return true;
 *   }
 * }
 * ```
 *
 * @package inhere\gearman\jobs
 */
abstract class RequestProxyJob extends UseLogJob
{
    /**
     * request method
     * @var string
     */
    protected $method = 'GET';

    /**
     * eg: http://api.domain.com
     * @var string
     */
    protected $baseUrl;

    /**
     * eg: /user/after-login
     * @var string
     */
    protected $path;

    /**
     * @param array $payload
     * @return bool
     */
    abstract protected function beforeSend(array &$payload);
    // {
    //      if (!isset($payload['userId']) || $payload['userId'] <= 0) {
    //          return false;
    //      }
    //
    //      $this->baseUrl = 'http://api.domain.com';
    //      $this->path = '/user/after-register';
    //
    //      return true;
    // }

    /**
     * doRun
     * @param $workload
     * @param \GearmanJob $job
     * @return mixed
     */
    protected function doRun($workload, \GearmanJob $job)
    {
        $this->info("Received workload=$workload");
        $payload = json_decode($workload, true);

        if (!$this->beforeSend($payload)) {
            $this->err("Data validate failed, workload=$workload");
            return false;
        }

        $method = strtolower($this->method);
        $baseUrl = $this->baseUrl;
        $path = $this->path;

        $this->info("Request method=$method baseUrl=$baseUrl path=$path data=" . json_encode($payload));

        $curl = new CurlHelper();
        $ret = $curl->setBaseUrl($baseUrl)->$method($path, $payload);
        // $ary = json_decode($ret, true);

        if ($this->resultValidate($ret, $curl)) {
            $this->info("Successful for the job, remote return=$ret");

            return true;
        }

        $this->err("Failed for the job, remote return=$ret, send=", [
            'method' => $method,
            'api' => $baseUrl . $path,
            'send' => $payload,
        ]);

        return false;
    }

    /**
     * resultValidate
     * @param  string     $result
     * @param  CurlHelper $curl
     * @return bool
     */
    protected function resultValidate($result, CurlHelper $curl)
    {
        return $curl->isOk();
    }
}

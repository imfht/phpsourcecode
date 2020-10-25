<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午4:39.
 */

namespace MiotApi\Api;

use MiotApi\Exception\JsonException;
use MiotApi\Util\Jsoner\JsonLastError;
use MiotApi\Util\Request;

class BaseApi
{
    /**
     * App-Id
     * 在开放平台申请: https://open.home.mi.com.
     *
     * @var
     */
    protected $appId;

    /**
     * Access-Token
     * 小米账号登录后的Oauth Token
     * 需要使用者自己实现小米oauth并获取到用户的access token
     * oauth使用的应用id一定要与 App-Id一致.
     *
     * @var
     */
    protected $accessToken;

    /**
     * 名字空间
     * 必须是 miot-spec-v2.
     *
     * @var string
     */
    protected $specNS = 'miot-spec-v2';

    protected $httpClient;

    protected $host = 'api.home.mi.com';

    protected $port = 443;

    protected $timeout = 10;

    /**
     * Api constructor.
     *
     * @param null   $appId
     * @param null   $accessToken
     * @param string $specNS
     */
    public function __construct($appId = null, $accessToken = null, $specNS = 'miot-spec-v2')
    {
        $this->appId = $appId;
        $this->accessToken = $accessToken;
        $this->specNS = $specNS;
    }

    /**
     * 设置 请求的 host
     * 默认为大陆host，如果要获取其他地区设备的时候，可以使用此方法设置host.
     *
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * 读取抽象设备列表.
     *
     * @param bool $compact 如果希望读取设备列表时，只想读取最简单的信息，compact设置为true
     *
     * @return array|mixed
     */
    public function devices($compact = false)
    {
        $params = [];
        if ($compact) {
            $params = [
                'compact' => $compact,
            ];
        }

        return $this->get('/api/v1/devices', $params);
    }

    /**
     * GET.
     *
     * @param $uri
     * @param array $params
     *
     * @return array|bool|mixed
     */
    public function get($uri, $params = [])
    {
        $httpClient = $this->httpClient();

        $result = $httpClient
            ->setRequestURI($uri)
            ->setType('GET')
            ->setQueryParams($params)
            ->execute()
            ->getResponseText();

        if ($result) {
            $returnData = json_decode($result, true);
            $lastError = JsonLastError::check();

            return $returnData === null || !is_null($lastError) ? false : $returnData;
        } else {
            return [
                'status'  => '-705002036',
                'message' => $httpClient->getError(),
            ];
        }
    }

    /**
     * 获取http Client.
     *
     * @return Request
     */
    private function httpClient()
    {
        $this->httpClient = new Request($this->host, '', $this->port, true, $this->timeout);
        $this->httpClient->setHeader('App-Id', $this->appId);
        $this->httpClient->setHeader('Access-Token', $this->accessToken);
        $this->httpClient->setHeader('Spec-NS', $this->specNS);

        return $this->httpClient;
    }

    /**
     * 读取设备信息
     * 读取一个设备 : GET /api/v1/device-information?dids=xxxx
     * 读取多个设备： GET /api/v1/device-information?dids=xxxx,yyy,zzzzz.
     *
     * @param $dids
     *
     * @return array|mixed
     */
    public function deviceInformation($dids)
    {
        if (is_array($dids)) {
            $dids = implode(',', $dids);
        }

        $params = [
            'dids' => $dids,
        ];

        return $this->get('/api/v1/device-information', $params);
    }

    /**
     * 读取属性
     * 读取一个属性 : GET /api/v1/properties?pid=AAAD.1.1
     * 读取多个属性：GET /api/v1/properties?pid=AAAD.1.1,AAAD.2.3
     * 语音控制需要增加voice字段：GET /api/v1/properties?pid=AAAD.1.1,AAAD.2.3&voice={"recognition":"灯开了吗","semantics":"xxx"}.
     *
     * @param $pid
     * @param string $voice
     *
     * @return array|mixed
     */
    public function properties($pid, $voice = '')
    {
        if (is_array($pid)) {
            $pid = implode(',', $pid);
        }

        $params = [
            'pid' => $pid,
        ];

        if ($voice) {
            $params['voice'] = $voice;
        }

        return $this->get('/api/v1/properties', $params);
    }

    /**
     * 设置属性.
     *
     * @param $data
     *
     * @throws JsonException
     *
     * @return array|bool|mixed
     */
    public function setProperties($data)
    {
        if (!is_array($data)) {
            $data = json_decode($data, true);
            $lastError = JsonLastError::check();
            if (!is_null($lastError)) {
                throw new JsonException('It\'s not a json string.');
            }
        }

        $data = json_encode($data);

        return $this->put('/api/v1/properties', $data);
    }

    /**
     * PUT.
     *
     * @param $uri
     * @param $data
     *
     * @return array|bool|mixed
     */
    public function put($uri, $data)
    {
        $httpClient = $this->httpClient();
        $httpClient->setAdditionalCurlOpt(CURLOPT_POSTFIELDS, $data);

        $result = $httpClient
            ->setRequestURI($uri)
            ->setType('PUT')
            ->execute()
            ->getResponseText();

        if ($result) {
            $returnData = json_decode($result, true);
            $lastError = JsonLastError::check();

            return $returnData === null || !is_null($lastError) ? false : $returnData;
        } else {
            return [
                'status'  => '-705002036',
                'message' => $httpClient->getError(),
            ];
        }
    }

    /**
     * 调用方法
     * 一次请求只能调用一个设备的一个方法
     * PUT /api/v1/action.
     *
     * @param $data
     *
     * @throws JsonException
     *
     * @return array|bool|mixed
     */
    public function invokeActions($data)
    {
        if (!is_array($data)) {
            $data = json_decode($data, true);
            $lastError = JsonLastError::check();
            if (!is_null($lastError)) {
                throw new JsonException('It\'s not a json string.');
            }
        }

        $data = json_encode($data);

        return $this->put('/api/v1/action', $data);
    }

    /**
     * 读取用户在米家设置好的场景列表.
     *
     * @return array|mixed
     */
    public function scenes()
    {
        return $this->get('/api/v1/scenes');
    }

    /**
     * 主动触发某个场景.
     *
     * @param $sceneId
     *
     * @return array|bool|mixed
     */
    public function triggerScene($sceneId)
    {
        $data = [
            'id' => $sceneId,
        ];
        $data = json_encode($data);

        return $this->post('/api/v1/scene', $data);
    }

    /**
     * POST.
     *
     * @param $uri
     * @param $data
     *
     * @return array|bool|mixed
     */
    public function post($uri, $data)
    {
        $httpClient = $this->httpClient();
        $httpClient->setAdditionalCurlOpt(CURLOPT_POSTFIELDS, $data);

        $result = $httpClient
            ->setRequestURI($uri)
            ->setType('POST')
            ->execute()
            ->getResponseText();

        if ($result) {
            $returnData = json_decode($result, true);
            $lastError = JsonLastError::check();

            return $returnData === null || !is_null($lastError) ? false : $returnData;
        } else {
            return [
                'status'  => '-705002036',
                'message' => $httpClient->getError(),
            ];
        }
    }

    /**
     * 读取家庭列表.
     *
     * @return array|mixed
     */
    public function homes()
    {
        return $this->get('/api/v1/homes');
    }

    /**
     * 订阅属性变化
     * 开始订阅:
     * POST /api/v1/subscriptions
     * Content-Type: application/json
     * Content-Length: 134
     * ​ * {
     * "topic": "properties-changed",
     * "properties": [
     * "AAAB.1.1",
     * "AAAC.1.1",
     * "AAAD.1.1",
     * "AAAD.1.2"
     * ],
     * "receiver-url": "xxx"
     * }.
     *
     * 订阅成功，应答如下：
     * HTTP/1.1 207 Multi-Status
     * Content-Type: application/json
     * Content-Length: 156
     * {
     * "expired": 36000,    // 超时时间，单位为秒。
     * "properties": [
     * {
     * "pid": "AAAB.1.1",
     * "status": 0
     * },
     * {
     * "pid": "AAAC.1.1",
     * "status": -704002023
     * },
     * {
     * "pid": "AAAD.1.1",
     * "status": 0
     * }
     * {
     * "pid": "AAAD.1.2",
     * "status": 705202023
     * }
     * ]
     * }
     *
     * @param $properties
     * @param $customData
     * @param $receiverUrl
     *
     * @return array|bool|mixed
     */
    public function subscript($properties, $customData, $receiverUrl)
    {
        $data = [
            'topic'        => 'properties-changed',
            'properties'   => $properties,
            'custom-data'  => $customData,
            'receiver-url' => $receiverUrl,
        ];
        $data = json_encode($data);

        return $this->post('/api/v1/subscriptions', $data);
    }

    /**
     * 退订属性变化
     * POST /api/v1/subscriptions
     * Content-Type: application/json
     * Content-Length: 134
     * ​ * {
     * "topic": "properties-changed",
     * "properties": [
     * "AAAB.1.1",
     * "AAAC.1.1",
     * "AAAD.1.1",
     * "AAAD.1.2"
     * ],
     * "receiver-url": "xxx"
     * }.
     *
     * 退订成功，应答如下：
     * HTTP/1.1 207 Multi-Status
     * Content-Type: application/json
     * Content-Length: 156
     * {
     * "expired": 36000,    // 超时时间，单位为秒。
     * "properties": [
     * {
     * "pid": "AAAB.1.1",
     * "status": 0
     * },
     * {
     * "pid": "AAAC.1.1",
     * "status": -704002023
     * },
     * {
     * "pid": "AAAD.1.1",
     * "status": 0
     * }
     * {
     * "pid": "AAAD.1.2",
     * "status": 705202023
     * }
     * ]
     * }
     *
     * @param $properties
     *
     * @return array|bool|mixed
     */
    public function unSubscript($properties)
    {
        $data = [
            'topic'      => 'properties-changed',
            'properties' => $properties,
        ];
        $data = json_encode($data);

        return $this->delete('/api/v1/subscriptions', $data);
    }

    /**
     * DELETE.
     *
     * @param $uri
     * @param $data
     *
     * @return array|bool|mixed
     */
    public function delete($uri, $data)
    {
        $httpClient = $this->httpClient();
        $httpClient->setAdditionalCurlOpt(CURLOPT_POSTFIELDS, $data);

        $result = $httpClient
            ->setRequestURI($uri)
            ->setType('DELETE')
            ->execute()
            ->getResponseText();

        if ($result) {
            $returnData = json_decode($result, true);
            $lastError = JsonLastError::check();

            return $returnData === null || !is_null($lastError) ? false : $returnData;
        } else {
            return [
                'status'  => '-705002036',
                'message' => $httpClient->getError(),
            ];
        }
    }
}

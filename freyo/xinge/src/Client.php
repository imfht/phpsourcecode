<?php

namespace Freyo\Xinge;

use Freyo\Xinge\Client\XingeApp;
use Freyo\Xinge\Exceptions\CouldNotSendNotification;
use Illuminate\Support\Facades\Log;

class Client
{
    const SUCCESSFUL_SEND = 0;

    /**
     * @var XingeApp
     */
    private $app;

    /**
     * @param XingeApp $app
     */
    public function __construct(XingeApp $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return array
     */
    public function __call($method, $arguments)
    {
        Log::debug($arguments);

        $response = $this->app->{$method}(...$arguments);

        Log::debug($response);

        return $this->handleProviderResponses($response);
    }

    /**
     * @param array $response
     *
     * @throws CouldNotSendNotification
     *
     * @return array
     */
    protected function handleProviderResponses($response)
    {
        $errorCode = (int) array_get($response, 'ret_code');

        if ($errorCode !== self::SUCCESSFUL_SEND) {
            throw CouldNotSendNotification::serviceRespondedWithAnError(
                (string) array_get($response, 'err_msg'),
                $errorCode
            );
        }

        return $response;
    }
}

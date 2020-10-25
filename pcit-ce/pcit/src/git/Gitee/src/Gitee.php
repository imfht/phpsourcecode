<?php

declare(strict_types=1);

namespace PCIT\Gitee;

use Curl\Curl;
use PCIT\GPI\GPI;
use TencentAI\TencentAI;

class Gitee extends GPI
{
    public $class_name = 'Gitee';

    public function __construct(TencentAI $tencent_ai, ?string $access_token = null)
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }

        $this['config'] = [
            'api_url' => config('git.gitee.api_url'),
            'client_id' => config('git.gitee.oauth.client_id'),
            'client_secret' => config('git.gitee.oauth.client_secret'),
            'callback_url' => config('git.gitee.oauth.callback_url'),
            'access_token' => $access_token,
        ];

        $this['curl_config'] = $access_token ? [null, false,
            [
                'Authorization' => 'token '.$this['config']['access_token'],
            ],
        ] : [];

        $this['curl_timeout'] = 1 * 60;
        $curl = new Curl(...$this['curl_config']);
        $curl->setTimeout($this['curl_timeout']);

        $this['curl'] = $curl;
        $this['tencent_ai'] = $tencent_ai;
    }
}

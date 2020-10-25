<?php

namespace app\Uploaders;

use GuzzleHttp\Client;

abstract class AbstractUploader
{
    public $api;

    public $baseUrl;

    public $httpClient;

    public function __construct($config = [])
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }

        $this->httpClient = new Client();
    }

    abstract public function upload(string $file);
}

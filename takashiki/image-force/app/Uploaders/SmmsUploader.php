<?php

namespace App\Uploaders;

class SmmsUploader extends AbstractUploader
{
    public $api = 'https://sm.ms/api/upload';

    public $baseUrl = 'ooo.0o0.ooo';

    public function upload(string $file)
    {
        $response = (string) $this->httpClient->post($this->api, [
            'multipart' => [
                [
                    'name' => 'smfile',
                    'contents' => fopen($file, 'r'),
                ],
            ],
        ])->getBody();

        $info = json_decode($response);

        return isset($info->code) && $info->code == 'success' ?
            $this->baseUrl.$info->data->path :
            false;
    }
}

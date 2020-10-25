<?php

namespace App\Uploaders;

class SinaUploader extends AbstractUploader
{
    public $api = 'http://x.mouto.org/wb/x.php?up';
    public $baseUrl = 'ws4.sinaimg.cn/large/';

    public function upload(string $file)
    {
        $response = (string) $this->httpClient->post($this->api, [
            'body' => fopen($file, 'r'),
        ])->getBody();

        $info = json_decode($response);

        return empty($info->pid) ? false : $this->baseUrl.$info->pid;
    }
}

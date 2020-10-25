<?php

namespace App\Uploaders;

class NiupicUploader extends AbstractUploader
{
    public $api = 'https://www.niupic.com/upload2.php';
    
    public $baseUrl = 'i.niupic.com/';

    public function upload(string $file)
    {
        $response = (string) $this->httpClient->post($this->api, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($file, 'r'),
                ],
            ],
        ])->getBody();

        $info = json_decode(preg_replace('/[[:^print:]]/', '', $response));

        return isset($info->status) && $info->status == 'success' ?
            $this->baseUrl.$info->file_images :
            false;
    }
}

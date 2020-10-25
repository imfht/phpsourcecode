<?php

namespace App\Handlers;

use App\Http\Controllers\Api\Traits\BaseResponseTrait;
use App\Models\Attachment;
use Illuminate\Support\Facades\Config;
use OSS\OssClient;
use OSS\Core\OssException;

class OssHandler
{
    use BaseResponseTrait;

    protected $status = true;
    protected $message = '文件上传成功';
    protected $data = [];
    protected $m_attachment;

    protected $config;
    protected $ossClient;
    protected $base_image_up_dir = 'images';

    public function __construct()
    {
        $this->config = config('filesystems.disks.oss');
        $this->ossClient = new OssClient($this->config['access_key_id'], $this->config['access_key_secret'], $this->config['endpoint']);
        $this->m_attachment = new Attachment();
    }

    public function uploadImageToOss($file, $user_id, $path = 'tests', $baseFileName = '')
    {
        if ($path === 'agreements') {
            $up_dir = $this->base_image_up_dir . '/' . $path;
        } else {
            $up_dir = $this->base_image_up_dir . '/' . $path . '/' . date('Y') . '/' . date('m');
        }

        $originalName = $file->getClientOriginalName();
        $extension = explode('.', $originalName)[1];

        if ($baseFileName) {
            $file_name = $baseFileName . '.' . $extension;
        } else {
            $file_name = md5($file->getFilename()) . rand(1000, 100000) . '.' . $extension;
        }

        $file_size = round($file->getClientSize() / 1000, 2);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) return $this->baseFailed('只能上传图片');

        try {
            $res_oss = $this->ossClient->uploadFile($this->config['bucket'], $up_dir . '/' . $file_name, $file);
            $this->ossClient->signUrl($this->config['bucket'], $up_dir . '/' . $file_name, Config::get('set_time.oss_image_expires')['time']);

        } catch (OssException $e) {
            return $this->baseFailed($e->getMessage());
        }

        $min_type = $file->getClientMimeType();
//        $url = $res_oss['info']['url'];
        $inser_data = [
            'user_id' => $user_id,
            'ip' => '127.0.0.1',
            'original_name' => $originalName,
            'mime_type' => $min_type,
            'size' => $file_size, // kb
            'type' => $path,
            'storage_position' => 'oss',
            'domain' => $up_dir . '/' . $file_name,
            'link_path' => '',
            'storage_name' => '',
        ];
        $inser_data['url'] = $this->showSignUrl($inser_data['domain'])['data'];

        try {
            $rest_insert_attachment_table = $this->m_attachment->saveData($inser_data);
            $this->data = array_merge($inser_data, ['attachment_id' => $rest_insert_attachment_table->id]);
        } catch (\Exception $e) {
            $this->message = $e;
            return $this->baseFailed($this->data, $this->message);
        }
        return $this->baseSucceed($this->data, '图片上传成功');

    }

    public function uploadImageToOssByBase64($file, $path = 'tests', $baseFileName = '', $extension)
    {
        if ($path === 'agreements') {
            $up_dir = $this->base_image_up_dir . '/' . $path;
        } else {
            $up_dir = $this->base_image_up_dir . '/' . $path . '/' . date('Y') . '/' . date('m');
        }

        if ($baseFileName) {
            $file_name = $baseFileName . '.' . $extension;
        } else {
            $file_name = md5($file->getFilename()) . rand(1000, 100000) . '.' . $extension;
        }

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) return $this->baseFailed('只能上传图片');

        $domain = $up_dir . '/' . $file_name;
        try {
            $res_oss = $this->ossClient->uploadFile($this->config['bucket'], $domain, $file);
            $this->ossClient->signUrl($this->config['bucket'], $domain, Config::get('set_time.oss_image_expires')['time']);

        } catch (OssException $e) {
            return $this->baseFailed($e->getMessage());
        }

        $url = $this->showSignUrl($domain)['data'];

        return $this->baseSucceed(['url' => $url, 'domain' => $domain], '图片上传成功');

    }


    public function showSignUrl($url)
    {
        try {
            $signedUrl = $this->ossClient->signUrl($this->config['bucket'], $url, Config::get('set_time.oss_image_expires')['time']);
        } catch (OssException $e) {
            return $this->baseFailed();
        }
        return $this->baseSucceed($signedUrl);
    }

    /**
     * 列出Bucket内所有目录和文件， 根据返回的nextMarker循环调用listObjects接口得到所有文件和目录
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    function listAllObjects()
    {
        while (true) {
            try {
                $listObjectInfo = $this->ossClient->listObjects($this->config['bucket'], []);
            } catch (OssException $e) {
                printf(__FUNCTION__ . ": FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表。
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            $listPrefix = $listObjectInfo->getPrefixList();
            if (!empty($listObject)) {
                print("objectList:\n");
                foreach ($listObject as $objectInfo) {
                    print($objectInfo->getKey() . "\n");
                }
            }
            if (!empty($listPrefix)) {
                print("prefixList: \n");
                foreach ($listPrefix as $prefixInfo) {
                    print($prefixInfo->getPrefix() . "\n");
                }
            }
            if ($nextMarker === '') {
                break;
            }
        }
    }

}

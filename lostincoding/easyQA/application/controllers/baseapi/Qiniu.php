<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/vendor/autoload.php';
use Qiniu\Auth;

/**
 * 七牛控制器
 */
class Qiniu extends BaseAPI_Controller
{
    private $qiniu_config = null;
    public function __construct()
    {
        parent::__construct();
        $this->qiniu_config = $this->config->config['qiniu'];
    }

    /**
     * 生成上传Token
     */
    public function uptoken()
    {
        $accessKey = $this->qiniu_config['accessKey'];
        $secretKey = $this->qiniu_config['secretKey'];
        $bucket = $this->input->get('bucket');
        $type = $this->input->get('type');
        $key = $this->input->get('key');

        if (empty($key) || empty($type)) {
            return;
        }

        //验证图片key格式
        if ($bucket == $this->qiniu_config['static_bucket_name'] && $type == 'attachment') {
            if (!preg_match('/^(attachment)\/\d+\.(jpg|png|gif)$/', $key)) {
                return;
            }
        } else {
            return;
        }

        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);
        if (!empty($key)) {
            $policy = array(
                'scope' => $bucket . ':' . $key,
            );
            // 生成覆盖上传Token
            $uptoken = $auth->uploadToken($bucket, $key, 3600, $policy);
        } else {
            // 生成上传Token
            $uptoken = $auth->uploadToken($bucket);
        }
        $this->result['uptoken'] = $uptoken;
    }
}

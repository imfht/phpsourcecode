<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;


use Qiniu\Auth;

class Qiniu
{
    // KEY值
    private $accessKey;
    // 密钥
    private $secretKey;
    // 存储空间名
    private $bucket;
    // 绑定域名
    private $domain;
    // token令牌
    private $token;

    /**
     * 构造函数
     * Qiniu constructor.
     * @param string $bucket
     * @param string $domain
     */
    public function __construct($bucket = '', $domain = '')
    {
        // 七牛云配置信息
        $config = config('upload.qiniu');
        $this->accessKey = $config['ACCESS_KEY'];
        $this->secretKey = $config['SECRET_KEY'];
        $this->bucket = $config['BUCKET'];
        $this->domain = $config['DOMAIN'];
        // 认证
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传Token
        $this->token = $auth->uploadToken($bucket);
    }

    /**
     * 获取下载凭证
     * @param $file
     * @return string
     * @author 牧羊人
     * @date 2019/11/20
     */
    public function getFileUrl($file)
    {
        // 时间戳生成
        $now = time();
        $date = $now + 24 * 3600;
        // 下载凭证生成
        $url = "http://" . $this->domain . $file . "?e=" . $date;
        $sign = hash_hmac("sha1", $url, $this->accessKey, true);
        $encodedSign = base64_encode($sign);
        $token = $this->accessKey . ":" . $encodedSign;
        $url = $url . "&token=" . $token;
        return $url;
    }

    /**
     * 上传
     * @param array $file 图片参数
     * @return array
     */
    public function uploadOne($file)
    {
        // 构建 UploadManager 对象
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($this->token, $file['name'], $file['tmp_name']);
        if ($err !== null) {
            return ['err' => 1, 'msg' => $err, 'data' => ''];
        } else {
            //返回图片的完整URL
            return ['err' => 0, 'msg' => '上传完成', 'data' => ($this->domain . $ret['key'])];
        }
    }
}
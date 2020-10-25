<?php
/**
 * 七牛操作类
 * Date: 2018\3\31 0031 22:21
 */

namespace extend;

class Qiniu
{
    const QINIU_RS = 'http://rs.qiniu.com';
    const API_HOST = 'http://api.qiniu.com';
    /**
     * @var 初始化实力
     */
    protected static $instance;
    /**
     * 参数配置
     *
     * @var array
     */
    protected static $_config = null;

    protected  $_bucket;

    protected  $_url;

    protected  $_timeKey;

    protected function __construct($app)
    {
        if (is_null(self::$_config)) {
            self::$_config = $config = \Config::get('qiniu.qiniu');
        }else{
            $config = self::$_config;
        }
        $this->_bucket = isset($config[$app]['bucket']) ? $config[$app]['bucket'] : die('请配置bucket');
        $this->_url = isset($config[$app]['url']) ? $config[$app]['url'] : die('请配置URL');
        $this->_timeKey = isset($config[$app]['time_key']) ? $config[$app]['time_key'] : (isset($config['time_key']) ? $config['time_key'] : '');

    }

    /**
     * 初始化
     * @param $app
     * @return 初始化实力|static
     */
    public static function getInstance($app)
    {
        if (is_null(self::$instance[$app])) {
            self::$instance[$app] = new static($app);
        }
        return self::$instance[$app];
    }

    /**
     * 获取文件下载链接
     *
     * @param string $domain 应用bucket
     * @param string $name [文件名]
     * @param string $param [附加参数]
     *
     * @return string url
     */
    public function downloadUrl($name, $param = array())
    {
        $url = $this->_url . $name . '?' . http_build_query($param);
        $token = self::sign($url);
        return $url . '&token=' . $token;
    }

    /**
     * 构建时间戳防盗链鉴权的访问外链
     *
     * @param string $rawUrl 需要签名的资源url
     * @param string $encryptKey 时间戳防盗链密钥
     * @param string $durationInSeconds 链接的有效期（以秒为单位）
     *
     * @return string 带鉴权信息的资源外链，参考 examples/cdn_timestamp_antileech.php 代码
     */
    public function createTimestampAntiLeechUrl($rawUrl, $durationInSeconds = 1200, $encryptKey = null)
    {
        $rawUrl = $this->buildComUrl($rawUrl);
        $parsedUrl = parse_url($rawUrl);
        $deadline = time() + $durationInSeconds;
        $expireHex = dechex($deadline);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $encryptKey = $encryptKey ? $encryptKey : (isset($this->_timeKey) ? $this->_timeKey : die('请配置时间戳防盗链密钥'));
        $strToSign = $encryptKey . $path . $expireHex;
        $signStr = md5($strToSign);
        if (isset($parsedUrl['query'])) {
            $signedUrl = $rawUrl . '&sign=' . $signStr . '&t=' . $expireHex;
        } else {
            $signedUrl = $rawUrl . '?sign=' . $signStr . '&t=' . $expireHex;
        }

        return $signedUrl;
    }

    /**
     * 获取上传token
     *
     * @param mixed $bucket 存储位置
     * @param mixed $key 文件名
     * @param mixed $max 文件上限
     * @param int $timeout 过期时间
     *
     * @return array
     */
    public function getToken($key, $timeout = 1800, $bucket = '')
    {
        $setting = array(
            'scope' => $bucket ? $bucket : $this->_bucket,
            'saveKey' => $key,
            'deadline' => $timeout + $_SERVER['REQUEST_TIME'],
        );
        $setting = self::qiniuEncode(json_encode($setting));
        $token = self::sign($setting) . ':' . $setting;
        $result = [
            'key' => $key,
            'token' => $token
        ];
        return $result;
    }

    /**
     * 删除
     *
     * @param string $uri [文件在bucket里的路径]
     *
     * @return bool [description]
     */
    public function delete($uri)
    {
        $en = $this->_bucket . ':' . $uri;
        $file = self::qiniuEncode($en);
        return self::opration('/delete/' . $file);
    }

    /**
     * 转码为视频码
     * @param $uri
     * @param $notify_url
     * @param string $pipeline
     * @return bool
     */
    public function tomp4($uri, $notify_url, $pipeline = 'mzhua_video')
    {
        //队列 https://portal.qiniu.com/dora/create-mps 在此创建
        $bucket = $this->_bucket;
        $fops = "avthumb/mp4/s/1280x720/vb/1024k/vcodec/libx264|saveas/" . $this->qiniuEncode($bucket . ":" . $uri . "_hd.mp4");
        $params = array(
            'bucket' => $bucket,
            'key' => $uri,
            'fops' => $fops,
            'pipeline' => $pipeline,
            'notifyURL' => $notify_url
        );
        $data = http_build_query($params);
        $op = '/pfop/';
        $response = self::opration($op, $data, self::API_HOST);
        if ($response) {
            $r = json_decode($response, true, 512);
            $id = $r['persistentId'];
            return $id;
        }
        return false;
    }

    /**
     * 七牛操作
     *
     * @param string $op [操作命令]
     * @param null|array $data
     * @param string $host 主机
     *
     * @return bool [操作结果]
     */
    private function opration($op, $data = null, $host = self::QINIU_RS)
    {
        $token = self::sign(is_string($data) ? $op . "\n" . $data : $op . "\n");
        $url = $host . $op;
        $header = array('Content-Type:  application/x-www-form-urlencoded', 'Authorization: QBox ' . $token);
        if ($ch = curl_init($url)) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            if ($data) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $response = curl_exec($ch);
            //响应头
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            curl_close($ch);
            if ($status == 200) {
                return $body ? $body : true;
            }
//          return ['status'=>$status,'body'=>$body,'response'=>$response];
            return false;
        }
    }

    /**
     * 获取url签名
     *
     * @param string $url url
     *
     * @return string 签名字符串
     */
    private function sign($url)
    {
        $config = self::$_config;
        $sign = hash_hmac('sha1', $url, $config['sk'], true);
        $ak = $config['ak'];
        return $ak . ':' . self::qiniuEncode($sign);
    }

    /**
     * 七牛安全编码
     *
     * @param string $str
     */
    private function qiniuEncode($str)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    /**
     * 构建完成的URL
     * @param $uri
     * @return string
     */
    private function buildComUrl($uri)
    {
        if (strpos($uri, 'https://') === false) {
            $uri = $this->_url . $uri;
        }
        return $uri;
    }
}
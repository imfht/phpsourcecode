<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs\Aliyun;

include_once dirname(__FILE__) . '/aliyun-php-sdk-core/Config.php';

use Illuminate\Support\Str;
use Sts\Request\V20150401 as Sts;

/**
 * 阿里云oss
 * Class AliyunOss
 * @package App\Libs
 */
class AliyunOss
{
    public function __construct()
    {
        $this->id = config('aliyun.oss.id');
        $this->secret = config('aliyun.oss.secret');
        $this->bucket = config('aliyun.oss.bucket');
    }

    /**
     * 获取web上传token
     * @param string $model
     * @return array
     */
    public function getWebToken($model = 'images')
    {
        $host = 'http://' . $this->bucket . '.' . config('aliyun.oss.endpoint');
        $end_time = time() + 300;

        $dt_str = date("c", $end_time);
        $mydatetime = new \DateTime($dt_str);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        $expiration = $expiration . "Z";

        $file_name = md5(time() . Str::random(10));
        $img_dir = 'upload';
        if (config('app.debug')) {
            $img_dir = 'dev_upload';
        }
        $dir = $img_dir . '/' . $model . '/' . substr($file_name, 0, 2) . '/' . substr($file_name, 2, 2) . '/' . substr($file_name, 4, 2) . '/';
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;
        $arr = array('expiration' => $expiration, 'conditions' => $conditions);

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->secret, true));

        $response = array();
        $response['accessid'] = $this->id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['dir'] = $dir;
        $response['domain'] = config('app.img_domain') . '/';//图片网址
        return $response;
    }

    /**
     * 获取sts凭据
     * @param string $model
     */
    public function getSts($model = 'images')
    {
        $region_id = config('aliyun.oss.region_id');
        $endpoint = config('aliyun.sts.endpoint');
        $rolearn = config('aliyun.sts.rolearn');
        // 只允许子用户使用角色
        \DefaultProfile::addEndpoint($region_id, $region_id, "Sts", $endpoint);
        $iClientProfile = \DefaultProfile::getProfile($region_id, $this->id, $this->secret);
        $client = new \DefaultAcsClient($iClientProfile);
        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = $rolearn;
        // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
        // 详情请参考《RAM使用指南》
        // 此授权策略表示读取所有OSS的只读权限
        $policy = <<<POLICY
                {
                  "Statement": [
                    {
                      "Effect": "Allow",
                       "Action": [
                         "oss:DeleteObject",
                         "oss:ListParts",
                         "oss:AbortMultipartUpload",
                         "oss:PutObject"
                       ],
                       "Resource": "*"
                    }
                  ],
                  "Version": "1"
                }
POLICY;
        $request = new Sts\AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds(3600);
        try {
            $response = $client->getAcsResponse($request);
            if (isset($response->Credentials)) {
                $res_data = json_encode($response->Credentials);
                $res_data = array_change_key_case(json_decode($res_data, true));
                return $res_data;
            }
            return false;
        } catch (ServerException $e) {
            return false;
        } catch (ClientException $e) {
            return false;
        }
    }
}

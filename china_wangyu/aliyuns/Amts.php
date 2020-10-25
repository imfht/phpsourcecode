<?php
/**
 * Created by PhpStorm.
 * User: china_wangyu@aliyun.com
 * Date: 2018/6/5
 * Time: 10:14
 *  *  *  *  ** 求职区 **
 *  期望城市： 成都
 *  期望薪资： 12k
 *
 *  个人信息
 *
 *  工作经验: 3年
 *  开发语言: PHP / Python
 *
 *  联系方式：china_wangyu@aliyun.com
 */

namespace aliyuns;

use aliyuns\amts\AmtsClient;
use aliyuns\amts\AmtsInput;
use aliyuns\amts\AmtsOutput;
use aliyuns\amts\AmtsPipeline;

/**
 * 阿里云转码类
 * Class AliyunMts
 * @package Aliyun 阿里云SDK库
 * @author china_wangyu@aliyun.com
 * @version 1.0.2
 * @inheritdoc 使用说明
 * use  aliyun\Amts;
 * Amts::instance(oss地域,阿里云授权access_key_id,阿里云授权access_key_secret,阿里云OSS bucket, 阿里云转码模板ID);
 * oss输入对象
 * $oss_input_object = 'video/live.mp4';
 * oss输出对象
 * $oss_output_object = [
 *      'oss_save_path' => 'video/'.time().'.mp4'
 *      ''
 * ];
 * Amts::runVideoTranscoding($oss_input_object,$oss_output_object);
 */
class Amts
{
    /**
     * 所属地区 默认：深圳
     * @var string
     */
    static private $mps_region_id = 'cn-shenzhen';
    /**
     * 阿里云access_key_id
     * @var string
     */
    static private $access_key_id = '';
    /**
     * 阿里云access_key_secret
     * @var string
     */
    static private $access_key_secret = '';
    /**
     * 模板ID 可选填
     * @defualt : S00000001-200010
     * @var string
     */
    static public $template_id = 'S00000001-200010';
    /**
     *  所属地区管道pipeline_id
     * @var string
     */
    static public $pipeline_id = '';
    /**
     * 所属地区oss站点
     * @var string
     */
    static public $oss_location = 'oss-cn-shenzhen';
    /**
     * 阿里云OSS bucket名称
     * @var string
     */
    static public $oss_bucket = '';
    /**
     * 阿里云OSS 需要转码视频文件地址
     * @var string
     * @type oos-filePath
     */
    static public $oss_input_object = '';
    /**
     * 阿里云OSS 需要 ·输出· 转码视频文件地址
     * @var string
     * @type oos-filePath
     */
    static public $oss_output_object = '';
    /**
     * 转码输出对象初始化
     * @var array
     */
    static private $outputs = [];
    /**
     * DefaultAcsClient实例并初始化对象
     * @object \AmtsClient
     */
    static private $client = [];
    /**
     * 创建API请求对象
     * @object  \Mts\SubmitJobsRequest
     */
    static private $request = [];

    /**
     * 初始化創建 DefaultAcsClient
     * @param $mps_region_id  【所属地区 默认：深圳】
     * @param $access_key_id  【阿里云access_key_id】
     * @param $access_key_secret  【阿里云access_key_secret】
     * @param string $template_id 【模板ID 可选填】
     * @param $bucket | 必填
     */
    public static function instance($mps_region_id, $access_key_id, $access_key_secret, $bucket, $template_id = '')
    {
        self::initClient($mps_region_id,$access_key_id,$access_key_secret);
        empty($bucket) && Json::error('oss_bucket is null~');
        self::$oss_bucket = $bucket;
        self::$oss_location = 'oss-' . self::$mps_region_id;
        self::$oss_bucket = $bucket;
        !empty($template_id) && self::$template_id = $template_id;
        self::initPipelineID();
    }

    /**
     * 执行实例化的转码对象
     * 0. 验证参数
     * 1. 创建请求对象 \Mts\SubmitJobsRequest
     * 2. 创建输出对象资源
     * 3. 执行转码
     */
    public static function runVideoTranscoding($oss_input_object, $oss_output_object)
    {

        self::initOutput($oss_output_object);
        self::initRequest($oss_input_object);
        self::execute();
    }

    /**
     * 创建DefaultAcsClient实例并初始化
     * @self::$client \DefaultAcsClient
     * @param $mps_region_id
     * @param $access_key_id
     * @param $access_key_secret
     */
    public static function initClient($mps_region_id,$access_key_id,$access_key_secret)
    {
        self::$client = AmtsClient::instance($mps_region_id,$access_key_id,$access_key_secret);
        self::$mps_region_id = $mps_region_id;
        self::$access_key_id = $access_key_id;
        self::$access_key_secret = $access_key_secret;
    }

    /**
     * 创建AmtsPipeline实例并获取管道ID
     */
    public static function initPipelineID()
    {
        $AmtsPipeline = AmtsPipeline::instance(self::$client);
        self::$pipeline_id = $AmtsPipeline::$pipeline_id;
    }

    /**
     * 初始化输出资源类型
     */
    public static function initOutput($outputParam = [])
    {
        if (empty($outputParam)) {
            $outputParam = [
                'oss_save_path' => 'video/' . time() . '.mp4',
            ];
        }
        $AmtsOutput = AmtsOutput::instance(self::$template_id, $outputParam);
        self::$outputs = $AmtsOutput::$outputArray;
    }

    /**
     * 创建API请求并设置参数
     * @self::$request Mts\SubmitJobsRequest
     */
    public static function initRequest($oss_input_path)
    {
        $inputParam = [
            'oss_location' => self::$oss_location,
            'oss_bucket' => self::$oss_bucket,
            'oss_input_object' => $oss_input_path,
        ];
        $AmtsInput = AmtsInput::instance(self::$outputs,$inputParam,self::$pipeline_id);
        self::$request = $AmtsInput::$instance;
    }



    /**
     * 执行视频转码
     */
    public static function execute()
    {
        # 发起请求并处理返回
        try {
            $response = self::$client->getAcsResponse(self::$request);
            if ($response->{'JobResultList'}->{'JobResult'}[0]->{'Success'}) {
                Json::success('Success', ['RequestId' => $response->{'RequestId'}, 'JobId' => $response->{'JobResultList'}->{'JobResult'}[0]->{'Job'}->{'JobId'}]);
            } else {
                Json::success('Success', ['']);
            }
        } catch (\ServerException $e) {
            Json::error($e->getMessage());
        } catch (\ClientException $e) {
            Json::error($e->getMessage());
        }
    }



}
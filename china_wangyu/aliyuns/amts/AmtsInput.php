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

namespace aliyuns\amts;

include_once __DIR__.'/../aliyun-php-sdk-core/Config.php';

use aliyuns\Json;
use Mts\Request\V20140618 as Mts;

/**
 * Class AmtsInput 转码对象输入类
 * @package aliyun\amts
 */
class AmtsInput extends Mts\SubmitJobsRequest
{
    /**
     * 实例化 \ SubmitJobsRequest对象
     * @var array
     */
    public static $instance = [];
    /**
     * 请求资源格式
     * @var string
     */
    private static $setAcceptFormat = 'JSON';

    /**
     * 管道ID AmtsPipeline :: $pipeline_id
     * @var $pipline_id
     */
    private static $piplineId;
    /**
     * 输出资源对象 AmtsOutput
     * @var $amtsOutput
     */
    private static $amtsOutput;

    /**
     * oss资源地域分类
     */
    private static $ossLocation;
    /**
     * oss储存Bucket名称
     * @var
     */
    private static $ossBucket;
    /**
     * oss输入资源路径
     * @var
     */
    private static $ossInputVideoPath;

    /**
     * 实例化请求对象
     * @param $amtsOutput
     * @param $inputParam
     * @param $pipline_id
     * @return AmtsInput
     */
    public static function instance($amtsOutput,$inputParam,$pipline_id):self
    {

        self::validate_param($amtsOutput,$inputParam,$pipline_id);
        return self::setSelf();
    }

    /**
     * 验证输出参数
     * @param $amtsOutput
     * @param $inputParam
     * @param $pipline_id
     */
    private static function validate_param($amtsOutput,$inputParam,$pipline_id)
    {

        if (empty($amtsOutput) or empty($inputParam) or empty($pipline_id)) Json::error('请输入对应参数 ~');
        self::$amtsOutput = $amtsOutput;
        self::$piplineId = $pipline_id;
        !isset($inputParam['oss_location']) && Json::error('请输入对应参数 oss_location ~');
        self::$ossLocation = $inputParam['oss_location'];
        !isset($inputParam['oss_bucket']) && Json::error('请输入对应参数 oss_bucket ~');
        self::$ossBucket = $inputParam['oss_bucket'];
        !isset($inputParam['oss_input_object']) && Json::error('请输入对应参数 oss_input_object ~');
        self::$ossInputVideoPath = $inputParam['oss_input_object'];
    }

    /**
     * 设置输出资源参数
     * @return AmtsInput
     */
    private static function setSelf():self
    {
        if (isset(self::$instance)){
            $request =  new parent();
            $request->setAcceptFormat(self::$setAcceptFormat);
            # Input
            $input = array('Location' => self::$ossLocation,
                'Bucket' => self::$ossBucket,
                'Object' => urlencode(self::$ossInputVideoPath));
            $request->setInput(json_encode($input));
            $request->setOUtputs(json_encode(self::$amtsOutput));
            $request->setOutputBucket(self::$ossBucket);
            $request->setOutputLocation(self::$ossLocation);
            # PipelineId
            $request->setPipelineId(self::$piplineId);
            self::$instance = $request;
        }
        return new self();
    }
}
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
 * Class AmtsPipeline 阿里云管道类
 * @package aliyun\amts
 */
class AmtsPipeline
{

    /**
     * 实例化 \ SubmitJobsRequest对象
     * @var array
     */
    public static $instance = [];
    /**
     * 当前管道ID
     * @var
     */
    public static $pipeline_id;
    /**
     * 实例化连接对象
     * @var
     */
    private static $client;


    /**
     * 实例化请求资源
     * @param \DefaultAcsClient $client
     * @return AmtsPipeline
     */
    public static function instance(\DefaultAcsClient $client):self
    {
        self::validate_param($client);
        return self::setSelf();
    }

    /**
     * 验证参数
     * @param \DefaultAcsClient $client
     */
    private static function validate_param(\DefaultAcsClient $client)
    {
        if (empty($client) ) Json::error('请输入有效转码资源对象或模板ID~');
        self::$client = $client;
    }

    /**
     * 设置输出资源参数
     * @return AmtsPipeline
     */
    private static function setSelf()
    {
        session_start([
            'cookie_lifetime' => 86400,
            'read_and_close'  => true,
        ]);
        isset($_SESSION['pipeline_id'])&& self::$pipeline_id = $_SESSION['pipeline_id'];
        if (self::$pipeline_id == ''){
            $request = new Mts\SearchPipelineRequest();
            $response = self::$client->getAcsResponse($request);
            $pipelines = json_decode(json_encode($response->PipelineList->Pipeline), true);
            $_SESSION['pipeline_id'] = $pipelines[0]['Id'];
            self::$pipeline_id = $pipelines[0]['Id'];
        }
        return new self();
    }
}
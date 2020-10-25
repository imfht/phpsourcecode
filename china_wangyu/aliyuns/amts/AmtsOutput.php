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

use aliyuns\Json;

/**
 * Class AmtsOutput 阿里云转码输出类
 * @package aliyun\amts
 */
class AmtsOutput
{
    /**
     * 实例化 \ SubmitJobsRequest对象
     * @var array
     */
    public static $instance = [];
    /**
     * 阿里云转码后缀格式
     * @var array
     */
    private static $instanceContainer = array('Format' => 'mp4');
    /**
     * 阿里云视频转码视频参数
     * @var array
     */
    private static $instanceVideo = array('Codec' => 'H.264',   # 阿里云视频转码格式
                                            'Bitrate' => 480,   # 阿里云清晰度比特率  例：480P
                                            'Width' => 640,     # 阿里云视频宽度640
                                            'Fps' => 25);       # 阿里云视频FPS值
    /**
     * 阿里云视频转码音频参数
     * @var array
     */
    private static $instanceAudio = array('Codec' => 'AAC',     # 阿里云音频格式
                                            'Bitrate' => 128,   # 阿里云音频清晰比特率
                                            'Channels' => 2,    # 阿里云音频渠道
                                            'Samplerate' => 44100); # 阿里云音频采样率
    /**
     * 阿里云视频转码模板ID
     * @var
     */
    private static $instanceTemplateId;
    /**
     * 阿里云输出
     * @var
     */
    private static $instanceOssOuputPath;
    /**
     * 阿里云输出资源
     * @var array
     */
    public static $outputArray = [];


    /**
     * 实例化请求对象输出资源
     * @param $template_id
     * @param array $oss_output_object
     * @return AmtsOutput
     */
    public static function instance($template_id,$oss_output_object = []):self
    {
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        self::validate_param($template_id,$oss_output_object);
        return self::setSelf();
    }

    /**
     * 验证输出参数
     * @param $template_id
     * @param $oss_output_object
     */
    private static function validate_param($template_id,$oss_output_object)
    {
        if (empty($template_id) or empty($oss_output_object) ) Json::error('请输入有效转码资源对象或模板ID~');
        self::$instanceTemplateId = $template_id;
        !isset($oss_output_object['oss_save_path']) && Json::error('请输入转码OSS资源对象保存路径~');
        self::$instanceOssOuputPath = $oss_output_object['oss_save_path'];
        isset($oss_output_object['Container']) && self::$instanceContainer = $oss_output_object['Container'];
        isset($oss_output_object['Video']) && self::$instanceVideo = $oss_output_object['Video'];
        isset($oss_output_object['Audio']) && self::$instanceAudio = $oss_output_object['Audio'];
    }

    /**
     * 设置输出资源参数
     * @return AmtsOutput
     */
    private static function setSelf():self
    {

        if (empty(self::$outputArray)){
            $output['OutputObject'] = urlencode(self::$instanceOssOuputPath);
            $output['Container'] =self::$instanceContainer;
            $output['Video'] = self::$instanceVideo;
            $output['Audio'] = self::$instanceAudio;
            $output['TemplateId'] = self::$instanceTemplateId;
            self::$outputArray = array($output);
        }
        return new self();
    }
}
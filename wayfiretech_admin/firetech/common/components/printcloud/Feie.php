<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-02 14:59:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-03 12:22:55
 */

namespace common\components\printcloud;

//公共方法类库
use Yii;
use yii\base\BaseObject;

 //以下参数不需要修改
 define('IP', 'api.feieyun.cn');      //接口IP或域名
 define('PORT', 80);            //接口IP端口
 define('PATH', '/Api/Open/');    //接口路径
 
class Feie extends BaseObject
{
    /*
     * @Perpose 定义接口请求地址
     */
    public $url = 'http://api.feieyun.cn/Api/Open/';

    /**
     * @var Client
     */
    public static $httpClient = null;

    /*
     * @Perpose 错误信息
     */
    public $errMsg = '';

    /*
     * @Perpose 打印机编号
     */
    public static $sn = '';

    /*
     * @Perpose ：飞鹅云后台注册账号
     */
    public static $USER = '';

    /*
     * @Perpose 飞鹅云后台注册账号后生成的UKEY
     */
    public static $UKEY = '';

    /*
     * @Perpose 初始化的时候设置好接口请求地址
     */
    public function init()
    {
        

        parent::init();

        $storeInfo = Yii::$app->service->commonGlobalsService->getStoreDetail(Yii::$app->params['store_id']);

        header('Content-type: text/html; charset=utf-8');

        define('USER', $storeInfo['USER']);  //*必填*：飞鹅云后台注册账号
        define('UKEY', $storeInfo['UKEY']);  //*必填*: 飞鹅云后台注册账号后生成的UKEY 【备注：这不是填打印机的KEY】
        define('SN', $storeInfo['SN']);      //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API

        self::$sn = $storeInfo['SN'];
        self::$UKEY = $storeInfo['UKEY'];
        self::$USER = $storeInfo['USER'];

        self::$httpClient = new HttpClient(IP,PORT);
    }

    /**
     * [批量添加打印机接口 Open_printerAddlist].
     *
     * @param [string] $printerContent [打印机的sn#key]
     *
     * @return [string] [接口返回值]
     */
    public static function printerAddlist($printerContent)
    {
        $time = time();         //请求时间
        $msgInfo = array(
      'user' =>self::$USER,
      'stime' => $time,
      'sig' => self::signature($time),
      'apiname' => 'Open_printerAddlist',
      'printerContent' => $printerContent,
    );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [打印订单接口 Open_printMsg].
     *
     * @param [string] self::$sn      [打印机编号sn]
     * @param [string] $content [打印内容]
     * @param [string] $times   [打印联数]
     *
     * @return [string] [接口返回值]
     */
    public static function printMsg($content, $times)
    {
        $time = time();         //请求时间
        $msgInfo = array(
            'user' =>self::$USER,
            'stime' => $time,
            'sig' => self::signature($time),
            'apiname' => 'Open_printMsg',
            'sn' => self::$sn,
            'content' => $content,
            'times' => $times, //打印次数
        );
        $client = self::$httpClient;
		
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            //服务器返回的JSON字符串，建议要当做日志记录起来
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [标签机打印订单接口 Open_printLabelMsg].
     *
     * @param [string] self::$sn      [打印机编号sn]
     * @param [string] $content [打印内容]
     * @param [string] $times   [打印联数]
     *
     * @return [string] [接口返回值]
     */
    public static function printLabelMsg($content, $times)
    {
        $time = time();         //请求时间
        $msgInfo = array(
            'user' =>self::$USER,
            'stime' => $time,
            'sig' => self::signature($time),
            'apiname' => 'Open_printLabelMsg',
            'sn' => self::$sn,
            'content' => $content,
            'times' => $times, //打印次数
        );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            //服务器返回的JSON字符串，建议要当做日志记录起来
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [批量删除打印机 Open_printerDelList].
     *
     * @param [string] self::$snlist [打印机编号，多台打印机请用减号“-”连接起来]
     *
     * @return [string] [接口返回值]
     */
    public static function printerDelList($snlist)
    {
        $time = time();         //请求时间
        $msgInfo = array(
      'user' =>self::$USER,
      'stime' => $time,
      'sig' => self::signature($time),
      'apiname' => 'Open_printerDelList',
      'snlist' => $snlist,
    );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [修改打印机信息接口 Open_printerEdit].
     *
     * @param [string] self::$sn       [打印机编号]
     * @param [string] $name     [打印机备注名称]
     * @param [string] $phonenum [打印机流量卡号码,可以不传参,但是不能为空字符串]
     *
     * @return [string] [接口返回值]
     */
    public static function printerEdit($name, $phonenum)
    {
        $time = time();         //请求时间
        $msgInfo = array(
      'user' =>self::$USER,
      'stime' => $time,
      'sig' => self::signature($time),
      'apiname' => 'Open_printerEdit',
      'sn' => self::$sn,
      'name' => $name,
      'phonenum' => $phonenum,
    );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [清空待打印订单接口 Open_delPrinterSqs].
     *
     * @param [string] self::$sn [打印机编号]
     *
     * @return [string] [接口返回值]
     */
    public static function delPrinterSqs()
    {
        $time = time();         //请求时间
        $msgInfo = array(
            'user' =>self::$USER,
            'stime' => $time,
            'sig' => self::signature($time),
            'apiname' => 'Open_delPrinterSqs',
            'sn' => self::$sn,
        );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [查询订单是否打印成功接口 Open_queryOrderState].
     *
     * @param [string] $orderid [调用打印机接口成功后,服务器返回的JSON中的编号 例如：123456789_20190919163739_95385649]
     *
     * @return [string] [接口返回值]
     */
    public static function queryOrderState($orderid)
    {
        $time = time();         //请求时间
        $msgInfo = array(
        'user' =>self::$USER,
        'stime' => $time,
        'sig' => self::signature($time),
        'apiname' => 'Open_queryOrderState',
        'orderid' => $orderid,
        );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [查询指定打印机某天的订单统计数接口 Open_queryOrderInfoByDate].
     *
     * @param [string] self::$sn   [打印机的编号]
     * @param [string] $date [查询日期，格式YY-MM-DD，如：2019-09-20]
     *
     * @return [string] [接口返回值]
     */
    public static function queryOrderInfoByDate($date)
    {
        $time = time();         //请求时间
        $msgInfo = array(
            'user' =>self::$USER,
            'stime' => $time,
            'sig' => self::signature($time),
            'apiname' => 'Open_queryOrderInfoByDate',
            'sn' => self::$sn,
            'date' => $date,
        );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [获取某台打印机状态接口 Open_queryPrinterStatus].
     *
     * @param [string] self::$sn [打印机编号]
     *
     * @return [string] [接口返回值]
     */
    public static function queryPrinterStatus()
    {
        $time = time();         //请求时间
        $msgInfo = array(
          'user' =>self::$USER,
          'stime' => $time,
          'sig' => self::signature($time),
          'apiname' => 'Open_queryPrinterStatus',
          'sn' => self::$sn,
        );
        $client = self::$httpClient;
        if (!$client->post(PATH, $msgInfo)) {
            return 'error';
        } else {
            $result = $client->getContent();
            return $result;
        }
    }

    /**
     * [self::signature 生成签名].
     *
     * @param [string] $time [当前UNIX时间戳，10位，精确到秒]
     *
     * @return [string] [接口返回值]
     */
    public static function signature($time)
    {
        return sha1(USER.UKEY.$time); //公共参数，请求公钥
    }
}

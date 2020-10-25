<?php
namespace app\common;

use think\facade\Log;
use Workerman\Autoloader;
use Workerman\Worker as BaseWorker;
use Workerman\Lib\Timer;

class Worker extends BaseWorker 
{
    public static $progressWorkerList = [];
    public static $progressControllerWorker = null;
    public static $channelWorker = null;

    public static $mimeTypeMap = [];

    /**
     * 当前设置而http监听站点
     *
     * @var array
     */
    public static $modelHttpChannel = [];

    /**
     * 当前线上的内网客户端
     * 
     * 二维数组,形式如下
     * ['client_key'=>['pid']]
     *
     * @var array
     */
    public static $insideClient = [];
    
    public static $outsideConnections = [];

    public static $responseMessage = [];

    public $webClient = [];


    public $uniqid = null;


}

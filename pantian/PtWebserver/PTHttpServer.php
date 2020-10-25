<?php

if(!class_exists('swoole_server')){
    echo 'ERROR: please install php extension for swoole',PHP_EOL;
    exit(1);
}
/**
 * Class PTHttpServer
 */
class PTHttpServer extends \swoole_http_server{
    /**
     * @var \swoole_table
     */
    public $sw_table = null;
    /**
     * 内存表的行数大小
     * @var int
     */
    private $sw_table_size = 1024;

    /**
     * @var \swoole_table
     */
    public $workerInfoTable = null;
    /**
     * @var \swoole_table
     */
    public $ConfigSWTable = null;
    
    static $ConnectNameKey = 'ConnectNumber';
    static $RequestingKey = 'Requesting';
    static $sessionKey = 'session';


    const HTTP_CONFIG_TABLE_KEY = 'ConfigTable';
    /**
     * @var \swoole_table
     */
    public $sessionTable=null;

    function __construct ($host='0.0.0.0',$port=80)
    {
        $this->sw_table = new \swoole_table($this->sw_table_size);

        /**
         * 内存表
         */
        $this->sw_table->column(self::$ConnectNameKey,\swoole_table::TYPE_INT,8);//链接量
        $this->sw_table->column(self::$RequestingKey,\swoole_table::TYPE_INT,8);//正在处理的请求
        $this->sw_table->create();

        $this->sw_table->set(self::$ConnectNameKey, [self::$ConnectNameKey=>0]);
        $this->sw_table->set(self::$RequestingKey, [self::$RequestingKey=>0]);

        $this->ConfigSWTable = new \swoole_table(1);
        $this->ConfigSWTable->column(self::HTTP_CONFIG_TABLE_KEY, \swoole_table::TYPE_STRING, 1024 * 5);
        $this->ConfigSWTable->create();
        //json 格式保存 服务器配置
        $this->ConfigSWTable->set(self::HTTP_CONFIG_TABLE_KEY,[self::HTTP_CONFIG_TABLE_KEY=>'']);

        $this->createWorkerInfoTable();

        parent::__construct($host, $port);

    }

    static $memoryKey = 'memory';
    function createWorkerInfoTable ()
    {

        $this->workerInfoTable = new \swoole_table($this->sw_table_size);
        $this->workerInfoTable->column(self::$memoryKey, \swoole_table::TYPE_INT,8);
        $this->workerInfoTable->create();
        $this->workerInfoTable->set(self::$memoryKey,[self::$memoryKey=>0]);

        $this->sessionTable=new \swoole_table(65536);
        $this->sessionTable->column(self::$sessionKey,\swoole_table::TYPE_STRING,1024*4);
        $this->sessionTable->column('time',\swoole_table::TYPE_INT,11);
        $this->sessionTable->create();

    }

    /**
     * 获取服务器配置
     * @return mixed
     */
    function getHttpConf ()
    {
        $arr = $this->ConfigSWTable->get(self::HTTP_CONFIG_TABLE_KEY);
        return json_decode($arr[self::HTTP_CONFIG_TABLE_KEY],true);
    }

    function setHttpConf (array $Arr)
    {
        if($Arr && is_array($Arr)){
            $json = json_encode($Arr);
            $this->ConfigSWTable->set(self::HTTP_CONFIG_TABLE_KEY,[self::HTTP_CONFIG_TABLE_KEY=>$json]);
            return true;
        }
        return false;
    }
}

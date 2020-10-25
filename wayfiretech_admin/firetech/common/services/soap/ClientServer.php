<?php

use common\services\BaseService;

/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-21 10:54:48
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-21 10:55:08
 */
namespace common\services\soap;

use common\helpers\soap\client\Client;
use common\services\BaseService;
 

class ClientServer extends BaseService{

    /**
     * @var char $name
     * @soap
    */
    public static $Username;

    /**
    * @var char $password
    * @soap
    */
    public static $Password;
    
    /**
    * @var char $headerParam
    * @soap
    */
    public static $headerParam;

    public function __construct($AuthenticationToken,$Username, $Password) {
       
        self::$Username = $Username;
        self::$Password = $Password;
        // 请求头
        // self::$headerParam = [
        //     $AuthenticationToken   => [
        //         'Username' => $Username,
        //         'Password' => $Password,
        //     ]
        // ];
        self::$headerParam = [
                'Username' => $Username,
                'Password' => $Password,
            
        ];
        parent::__construct();

    }
   
    public static function request($url,$func,$options=[])
    {
        ini_set('soap.wsdl_cache_enabled',0);
        $header = new \SoapHeader('wsse','Security',self::$headerParam, false);
        
        // $aHTTP['http']['header'] =  "User-Agent: PHP-SOAP/5.5.11\r\n";

        // $aHTTP['http']['header'].= "username: C1040001760102\r\n"."password: bcv2020\r\n";
        // $context = stream_context_create($aHTTP);
        $client = new Client([
            'url' => $url,
            'options' => [
                'encoding' => 'UTF-8',
                'exceptions'=>true,
                'cache_wsdl'=>WSDL_CACHE_NONE,
            ],
            'header'=> $header
        ]);
        
        try {
            $Res = $client->$func($options);
            
        } catch (\SoapFault $f) {
            echo "Error Message: {$f->getMessage()}";
        }
   
     
        return $client->__getLastRequest();
    }   
}

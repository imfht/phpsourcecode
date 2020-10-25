<?php
namespace Scabish\Abyss;

use SCS;
use Exception;
use Scabish\Tool\Kit;

/**
 * Scabish\Abyss\Client
 * Abyss客户端
 * 
 * @example
 * // Abyss配置(API层)
    'abyss' => [ // Abyss客户端key
        'url' => 'http://focrs.com/abyss/index.php', // API地址
        'credit' => [
            'web' => [ // web前端
                'key' => '29d03b2cd1df163770ead73c6e541322' // 密钥
            ],
            'mobile' => [ // 移动端
                'key' => '39d03b2cd1df363970ffd73c8e5413ce' // 密钥
            ]
        ],
        'passport' => 'Staff/Read', // 账号信息接口，设置为false则关闭自动读取该接口
        'setting' => 'Setting/Read', // 系统配置信息接口，设置为false则关闭自动读取该接口
    ],
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-12-12
 */
class Client {
    
    private static $_instance;
    
    private $_id;
    private $_key;
    private $_url;
    
    private $_passportId; // 账号Id
    private $_passport = null; // 账号信息
    private $_setting = null; // 系统配置
     
    private function __construct() {}
    
    public function __clone() {}
    
    /**
     * 获取Abyss客户端实例
     * 如果设置了passportId，则在接下来调用Sink方法时，会获取passport详细信息，passport获取api应在config.php的abyss配置项中指定：
     * ...
     * 'abyss' => [
     *     'client' => [
     *         'url' => 'http://focrs.com/abyss/index.php', // abyss服务地址
     *         'id' => 'web', // 客户端id
     *         'key' => '29d03b2cd1df263770ead73c6e541322', // 客户端key
     *         'passport' => 'Staff/Read', // 获取账号信息的接口
     *         'setting' => 'Setting/Lists', // 获取系统配置信息的接口 
     * ...
     * ]      
     * @param integer $passportId 账号Id
     * @return \Scabish\Abyss\Client
     */
    public static function Instance($passportId = 0) {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            $config = SCS::Instance()->abyss['client'];
            self::$_instance->_id = $config['id'];
            self::$_instance->_key = $config['key'];
            self::$_instance->_url = $config['url'];
            self::$_instance->_passportId = $passportId;
        }
        return self::$_instance;
    }
    
    /**
     * 向Abyss发送数据请求
     * 
     * @example
     * 假设api Order/Read返回的数据结构为：
     * [
     *    'id' => 1,
     *    'fdNumber' => '20160906022653',
     *    'fdAmount' => 15000,
     *    'customer' => [ // 客户信息
     *        'id' => 11,
     *        'fdName' => 'Focrs',
     *        'address' => [ // 客户地址信息
     *            'id' => 111,
     *            'fdAddress' => '199 jin liu RD, Jinshan dist, Shanghai',
     *            'fdPhone' => '110'
     *        ]
     *    ]
     *  ]
     * 
     * use \Scabish\Abyss\Client as Abyss;
     * 
     * 单API查询：
     * list($order) = Abyss::Instance()->Sink([
            'api' => 'Order/Read',
            'param' => ['id' => 1],
            //'return' => '.' // 默认原数据格式返回
            // 'return' => '.customer' // 只返回客户信息
            // 'return' => '.cusomer.address.fdPhone' // 只返回客户电话
       ]);
       list($orders) = Abyss::Instance()->Sink([
            'api' => 'Order/Page',
            'page' => 2, // 当前页码
            'size' => 10, // 每页显示数目
            //'return' => '.' // 默认原数据格式返回
            // 'return' => '.customer' // 只返回客户信息
            // 'return' => '.cusomer.address.fdPhone' // 只返回客户电话
       ]);
     *  
     * 多API查询：
     * list($order, $customer) = Abyss::Instance()->Sink([
            'api' => 'Order/Read',
            'param' => ['id' => $id],
            'bind' => ['::customerId' => '.fdCustomerId'], // 绑定一个属性
            // 'bind' => ['::customer' => '.customer'], // 绑定一个对象属性
            // 'bind' => ['::fdCustomerId' => '.customer.id'] // 绑定一个对象属性中的属性(支持更深层属性的绑定)
            'cache' => 10 // 缓存10秒
       ], [
            'api' => 'Customer/Read',
            'param' => ['id' => '::customerId'], // 使用绑定的属性值
            // 'param' => ['id' => '::customer.id'] // 对绑定的参数选择调用属性
            // 'param' => ['id' => '::fdCustomerId']
       ]);
       
       API查询返回开关：
       list($_) = Abyss::Instance()->Sink([
           'wait' => false, // 不需要确认返回结果，适用于允许数据丢失场景，如推送日志或消息
           'query' => [
               'api' => 'Log/Push',
               'param' => $_REQUEST
           ]
       ]);
       
       API查询事务处理开关：
       // Abyss默认关闭事务处理，当一个业务处理中夹杂了日志操作，关闭事务处理机制，能保证即使业务处理异常但日志仍可被顺利记录下来(前提是程序处理顺序为先日志后业务)
       // 比如在做在线交易，应确保能记录第三方支付平台发送异步通知日志，这样在后续处理订单状态时有据可查，并快速找出原因
       list($result) = Abyss::Instance()->Sink([
           'transaction' => false, // 不需要开启事务处理
           'query' => [
               'api' => 'Paypal/Notify', // 记录paypal返回的请求日志，并处理相应的交易业务
               'param' => ['request' => $_REQUEST]
           ]
       ]);
       
       // 事务开启场景：客户支付成功后加积分
       list($_, $result) = Abyss::Instance()->Sink([
           'transaction' => true, // 开启事务处理
           'query' => [[
               'api' => 'Order/Paid', // 订单状态变为已支付
               'param' => ['status' => PAID]
           ], [
               'api' => 'Customer/Update', // 客户积分加10
               'param' => ['credit' => '{credit+10}']
           ]]
       ]);
     * @param array $query
     * @return mixed
     * @throws Exception
     */
    public function Sink() {
        $wait = true;
        $transaction = false;
        $fetchPassport = false; // 是否获取账号信息
        $fetchSetting = false; // 是否读取配置信息
        $apiList = [];
        if(1 == func_num_args()) {
            $query = func_get_arg(0);
            if(isset($query['wait'])) $wait = $query['wait']; // 是否等待返回结果
            if(isset($query['transaction'])) $transaction = $query['transaction'];
            $apiList = isset($query['query']) ? (isset($query['query']['api']) ? [$query['query']] : $query['query']) : [$query];
        } else {
            foreach(func_get_args() as $arg) {
                if(is_array($arg)) {
                    array_push($apiList, $arg);
                }
            }
        }
        foreach($apiList as $api) {
            if(isset($api['size'])) SCS::Page()->size = $api['size'];
            if(isset($api['page'])) SCS::Page()->current = $api['page'];
        }
        
        $config = SCS::Instance()->abyss['client'];
        if(Kit::Valid('passport', $config) && $this->_passportId && is_null($this->_passport)) {
            $fetchPassport = true;
            array_push($apiList, [
	           'api' => $config['passport'],
	           'param' => ['id' => $this->_passportId]
            ]);
        }
        if(Kit::Valid('setting', $config) && is_null($this->_setting)) {
            $fetchSetting = true;
            array_push($apiList, ['api' => $config['setting']]);
        }
        $query = [
            'wait' => $wait,
            'transaction' => $transaction,
            'query' => $apiList
        ];
        
        // 生成key
        $time = time();
        $key = sha1($this->_id.'ABYSS'.$this->_key.'ABYSS'.$time).'.'.$time;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect: ']);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        
        // 验证信息放到请求头部分
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-ABYSS-ID: '.$this->_id, 'X-ABYSS-KEY: '.$key]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $wait ? 30000 : 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
        $response = curl_exec($ch);
        curl_close($ch);
        
        if(!$wait) return;
        if(is_numeric($response)) throw new Exception($response); // 为数值时调用json_encode()方法不会报错
        
        $result = json_decode($response);
        if($result) {
            if($result->status) {
                if($fetchSetting) {
                    $this->_setting = array_pop($result->data);
                    if($fetchPassport) $this->_passport = array_pop($result->data);
                } elseif($fetchPassport) {
                    $this->_passport = array_pop($result->data);
                }
                return $result->data;
            } else {
                throw new Exception($result->data, E_USER_ERROR); // 标识为可展示给用户看的错误信息
            }
        } else {
            throw new Exception($response);
        }
    }
    
    /**
     * 登录账号基本信息
     * @return mixed|boolean
     */
    public function Passport() {
        if(!$this->_passportId) return false;
        if(!is_null($this->_passport)) return $this->_passport;
        
        $config = SCS::Instance()->abyss['client'];
        Kit::Valid('passport', $config) && $this->Sink();
        return $this->_passport;
    }
    
    public function Setting() {
        $config = SCS::Instance()->abyss['client'];
        is_null($this->_setting) && Kit::Valid('setting', $config) && $this->Sink();
        return $this->_setting;
    }
}
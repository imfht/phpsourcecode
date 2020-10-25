<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * API接口基类
 * 
 * @author 牧羊人
 * @date 2018-08-29
 */
namespace API\Controller;
use Think\Controller;
use API\Model\UserModel;
//解决API文档跨域调试请求的问题
header("Access-Control-Allow-Origin: *");
class APIBaseController extends Controller {
    
    /**请求的参数*/
    protected $req,$reqId,$post;
    /** 当前登录的用户编号*/
    protected $userId;
    /** 当前用户基本信息*/
    protected $userInfo;
    //模型、服务
    protected $mod,$service;
    function __construct() {
        parent::__construct();
        
        $this->userId = 0;
        
        //初始化请求参数
        $this->initRequest();

    }
    
    /**
     * API入口
     * 
     * @author 牧羊人
     * @date 2018-08-29
     */
    function index() {
        $this->ajaxReturn(message(MESSAGE_OK,true,[
            'req'=>$_REQUEST
        ]));
    }
    
    /**
     * 初始化网络请求
     *
     * @author 牧羊人
     * @date 2018-08-29
     */
    protected function initRequest() {
        $reqId = substr(md5(time().\Zeus::getRandCode(10).rand(1,1000)) ,8, 16);
        $this->reqId = $reqId;
         
        //预加载配置
        //TODO...
        $this->req = $_REQUEST;
        $this->post = $_POST;
        $userId = $this->req['user_id'];
        $reqId = $this->reqId;
    
        //校验登录信息
        if ($userId) {
            $token = trim($this->req['token']);
            $userMod = new UserModel();
            $userInfo = $userMod->getInfo($userId);
            if (empty($token) || ($userInfo['token']!=$token)) {
                 
                if (!$this->userId) {
    
                    //被挤掉的情况
                    if($userInfo['token']!=$token) {
                        $this->jsonReturn(MESSAGE_NEEDLOGIN,false,[
                            'is_need_jump_login' =>1
                        ],99999);
                    }else{
                        $this->jsonReturn(MESSAGE_NEEDLOGIN,false,[
                            'is_need_jump_login' =>1
                        ]);
                    }
                }
            }
            if ($userInfo['is_enabled']!=1) {
                $this->jsonReturn(MESSAGE_USER_FIRBIDDEN,false);
            }
            $this->userId = $userId;
            $this->userInfo = $userInfo;
        }
    }
    
    /**
     * 登录检测
     *
     * @author 牧羊人
     * @date 2018-08-29
     */
    protected function needLogin() {
        if (!$this->userId) {
            $this->jsonReturn(MESSAGE_NEEDLOGIN,false,[
                'is_need_jump_login' =>1
            ]);
        }
    }
    
    /**
     * 初始化分页
     *
     * @author 牧羊人
     * @date 2018-08-28
     * @param $page 分页数
     * @param $perpage 每页数
     * @param $limit 限制条数
     */
    protected function  initPage(&$page, &$perpage, &$limit) {
        $page = (int) $this->req['page'];
        $perpage = (int) $this->req['perpage'];
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : 10;
        $startIndex = ($page-1)*$perpage;
        $limit = "{$startIndex}, {$perpage}";
    }
    
    /**
     * 输出JSON数据
     *
     * @author 牧羊人
     * @date 2018-08-29
     */
    protected function jsonReturn() {
        false && message();
        $arr = func_get_args();
        if (!is_array($arr[0])) {
            $result = call_user_func_array("message", $arr);
        } else {
            $result = $arr[0];
        }
        $code = $result[0];
        //格式化数组
        $result = $this->getStringArray($result);
    
        $result['request_id'] = $this->reqId;
        //加密成字符串输出
        //TODO...
        $output = json_encode($result);
        $crypt = getCryptDesObject();
        $output = $crypt->encrypt($output);
        //APP_DEBUG && $output = $crypt->decrypt($output);
        echo $output;
        exit();
    }
    
    /**
     * 格式化为字符串数组
     *
     * @author 牧羊人
     * @date 2018-08-29
     * @param unknown $array
     * @return string
     */
    private function getStringArray($array) {
        foreach ($array as $key=>$row) {
            if (is_array($row)) {
                $array[$key] = $this->getStringArray($row);
            }elseif(is_object($row)){
                //TODO...
            }else {
                $array[$key] = (string) $row;
            }
        }
        return $array;
    }
    
}
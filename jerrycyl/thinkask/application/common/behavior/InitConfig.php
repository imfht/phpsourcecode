<?php 
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\behavior;
use app\common\controller\Base;
use \think\Request;
use \think\Session;
use \think\Cookie;
use \think\Hook;
use \think\Lang;
use \think\Config;
// echo "当前模块名称是" . $request->module();
// echo "当前控制器名称是" . $request->controller();
// echo "当前操作名称是" . $request->action();
class InitConfig extends Base
{
    public function run(&$params)
    {
        if($this->request->module()=="install") return ;
        $this->initconfig();
        $this->seo($this->request->module(),$this->request->action());
    }
    private function seo($module,$action){
        switch (strtolower($module)) {
            ##问答
            case 'question':
                    switch (strtolower($action)) {
                        case 'index':
                            $seo['title'] = '问答-'.getset('site_name').'-thinkask问答社区';
                            $seo['keywords'] = getset('keywords');
                            $seo['description'] = getset('description');
                            break;
                         case 'detail':
                            $question_id = (int)decode(input('encry_id'));
                            if(!$questioninfo = cache('seo_question_detail'.$question_id)){
                                cache('seo_question_detail'.$question_id,$questioninfo = $this->getbase->getone('question',['where'=>['question_id'=>$question_id],['field'=>'question_content,question_detail']]));
                            }

                            $seo['title'] = $questioninfo['question_content'];
                            $seo['keywords'] = scws($questioninfo['question_content'],5,false,' ');##分词TITLE
                            $seo['description'] = msubstr(strip_tags($questioninfo['question_detail']),0,100);
                            break;
                    }
                break;
                ##后台
                case 'admin':
                    switch (strtolower($action)) {
                          default:
                            $seo['title'] = '后台管理-thinkask问答社区';
                            $seo['keywords'] = '';
                            $seo['description'] = '';
                            break;
                    }
                break;
            ##默认
             default:
                $seo['title'] = getset('site_name').'-thinkask问答社区';
                $seo['keywords'] = getset('keywords');
                $seo['description'] = getset('description');
                break;
        }
        $this->assign('seo',$seo);
    }

    private function initconfig(){
        // 配置文件增加设置
        $config = [
        'USER_AUTH_ON'          =>true,#是否需要认证
        'USER_AUTH_TYPE'        =>'',#认证类型
        'USER_AUTH_KEY'         =>'',#认证识别号
        'REQUIRE_AUTH_MODULE'   =>'',#需要认证模块
        'NOT_AUTH_MODULE'       =>'',#无需认证模块
        'USER_AUTH_GATEWAY'     =>'',#认证网关
        'RBAC_DB_DSN'           =>'',#数据库连接DSN
        'RBAC_ROLE_TABLE'       =>'',#角色表名称
        'RBAC_USER_TABLE'       =>'',#用户表名称
        'RBAC_ACCESS_TABLE'     =>'',#权限表名称
        'RBAC_NODE_TABLE'       =>'',#节点表名称
        'auth'    => [
        // 权限开关
        'auth_on'           => 1,
        // 认证方式，0为登录认证；1为实时认证；n为n分钟更新权限缓存。
        'auth_type'         => 1,

         ],
         //git.oschina.net配置
        'think_sdk_GIT' => [
            'app_key' => 'fK2l0B5mGyLzQzYH3pTs', //应用注册成功后分配的 APP ID
            'app_secret' => 'Yd4cgWPh60vmxXeakNvj5i8hNusn27c1', //应用注册成功后分配的KEY
            'callback' => getSiteUrl(). 'git',
        ],
        //有道笔记 Youdao
        'SDK_YOUDAO' => [
            'APP_KEY' => '37a4eea74a7e53ebe4845d03d2185695', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '50a5cb7c5549412193bffa192411b5fa', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'youdao',
        ],



       'TMPL_ACTION_ERROR'     =>  '/success', // 默认错误跳转对应的模板文件
        'TMPL_ACTION_SUCCESS'   =>  '/success', // 默认成功跳转对应的模板文件


        //腾讯QQ登录配置
        'SDK_QQ' => [
            'APP_KEY' => getset('qq_login_app_id'), //应用注册成功后分配的 APP ID
            'APP_SECRET' => '17a5a055e69bb12e3c880ca79049e901', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'qq',
        ],
        //新浪微博配置
        'SDK_SINA' => [
            'APP_KEY' => '3453155715', //应用注册成功后分配的 APP ID
            'APP_SECRET' => 'a09ddb87450a2d65337c8334dd99b810', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'sina',
        ],
        
        //支付宝登录
        'SDK_ALIPAY' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'alipay',
        ],
        //微信登录
        'SDK_WEIXIN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'weixin',
        ],
        
        //腾讯微博配置
        'SDK_TENCENT' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'tencent',
        ],
        
        //网易微博配置
        'SDK_T163' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 't163',
        ],
        //人人网配置
        'SDK_RENREN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'renren',
        ],
        //360配置
        'SDK_X360' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'x360',
        ],
        //豆瓣配置
        'SDK_DOUBAN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'douban',
        ],
        //Github配置
        'SDK_GITHUB' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'github',
        ],
        //Google配置
        'SDK_GOOGLE' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'google',
        ],
        //MSN配置
        'SDK_MSN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'msn',
        ],
        //点点配置
        'SDK_DIANDIAN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'diandian',
        ],
        //淘宝网配置
        'SDK_TAOBAO' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'taobao',
        ],
        //百度配置
        'SDK_BAIDU' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'baidu',
        ],
        //开心网配置
        'SDK_KAIXIN' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'kaixin',
        ],
        //搜狐微博配置
        'SDK_SOHU' => [
            'APP_KEY' => '', //应用注册成功后分配的 APP ID
            'APP_SECRET' => '', //应用注册成功后分配的KEY
            'CALLBACK' => getSiteUrl() . 'sohu',
        ],
        ];
        Config::set($config);
    }
 

}
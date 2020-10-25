<?php
/**
 * 同步登陆插件
 * @author jry
 */
 
namespace Addons\SyncLogin;
use Common\Controller\Addon;


class SyncLoginAddon extends Addon{

    public $info = array(
        'name' => 'SyncLogin',
        'title' => '第三方账号同步登陆',
        'description' => '第三方账号同步登陆',
        'status' => 1,
        'author' => 'yidian',
        'version' => '0.1'
    );

    public function install(){ 
        $prefix = C("DB_PREFIX");
        $model = D();
        $model->execute("DROP TABLE IF EXISTS {$prefix}sync_login;");
        $model->execute("CREATE TABLE {$prefix}sync_login ( `uid` int(11) NOT NULL,  `openid` varchar(255) NOT NULL,  `type` varchar(255) NOT NULL,  `access_token` varchar(255) NOT NULL,  `refresh_token` varchar(255) NOT NULL,  `status` tinyint(4) NOT NULL  )");
        /* 先判断插件需要的钩子是否存在 */
        $this->getisHook($this->info['name'], $this->info['name'], $this->info['description']);
        $this->getisHook('SyncBind', $this->info['name'], '获取第三方登录的信息,在用户中心显示');
        $this->getisHook('SyncRegister', $this->info['name'], '第三方用户登陆后返回注册的页面');
        $this->getisHook('extend_user_nav', $this->info['name'], '用户中心左侧导航');
        return true;
    }

    public function uninstall(){
        $model = D();
        //删除钩子
        $this->deleteHook($this->info['name']);
        $prefix = C("DB_PREFIX");
        $model->execute("DROP TABLE IF EXISTS {$prefix}sync_login;");
        return true;
    }

    /*登录按钮钩子*/
    public function SyncLogin($param){
        $this->assign($param);
        $config = $this->getConfig();
        $this->assign('config',$config);
        $this->display('View/Default/login');
    }

    /*绑定钩子*/
    public function SyncBind(){
        $config = $this->getConfig();
        $this->assign('config',$config);
        $this->display('View/Default/binding');
    }
    
    /*载入模板文件钩子*/
    public function SyncRegister($uid){
        $user = session( 'user' );
        $token = session( 'token' );
        if (isset($user) && isset($token)) {
            $data = array(
                'uid' => $uid,
                'type' => session('user.type'),
                'openid' => session('token.openid'),
                'access_token' => session('token.access_token'),
                'refresh_token' => session('token.refresh_token'),
                'status' => 1,
            );
            D('SyncLogin')->add($data);
        }
    }

    /*用户中心左导航钩子*/
    public function extend_user_nav(){
        $leftnav = array('config'=> array('name'=>'第三方账号','url'=>addons_url('SyncLogin://Member/SyncLogin'),'icon'=>''));
        C('extend_user_nav.sysnclogin',$leftnav);
    }
}
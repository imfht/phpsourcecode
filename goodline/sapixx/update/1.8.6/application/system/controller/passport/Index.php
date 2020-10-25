<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 会员管理首页
 */
namespace app\system\controller\passport;
use app\common\event\Passport;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMember;
use app\common\model\SystemMiniapp;
use app\system\event\AppConfig;
use think\facade\Request;

class Index extends Common{

    /**
     * 后台主框架
     */
    public function index(){
        $condition = [];
        $condition['is_lock'] = 0;
        if($this->user->parent_id){
            $condition['id'] = $this->user->bind_member_miniapp_id;
        }else{
            $condition['member_id'] = $this->user->id;
        }
        $lists = SystemMemberMiniapp::where($condition)->field('id,miniapp_id,appname,head_img,miniapp_head_img,mp_head_img,is_lock')->order('id desc')->select();
        $apps = [];
        foreach ($lists as $key => $value) {
            $apps[$key]['id']      = $value->id;
            $apps[$key]['appname'] = $value->appname;
            $apps[$key]['is_lock'] = $value->is_lock;
            if(empty($value->head_img)){
                switch ($value->miniapp->types) {
                    case 'mp':
                        $head_img = $value->mp_head_img;
                        break;
                    case 'program':
                        $head_img = $value->miniapp_head_img;
                        break;
                    case 'app':
                        $head_img = $value->head_img;
                        break;
                    default:
                        $head_img = empty($value->mp_head_img) ? $value->mp_head_img : $value->miniapp_head_img;
                        break;
                }
            }else{
                $head_img = $value->head_img;;
            }
            $apps[$key]['logo'] = empty($head_img) ? "/static/{$value->miniapp->miniapp_dir}/logo.png" : $head_img;
        }
        $view['apps'] = $apps;
        if ($this->member_miniapp_id){
            $view['appname'] = $this->member_miniapp->appname;
            $view['welcome'] = url($this->member_miniapp->miniapp->miniapp_dir.'/manage.index/index');
        }else{
            $view['appname'] = '应用中心';
            $view['welcome'] = url('system/passport.appshop/index');
        }
        //判断帐号是否设置手机号
        return view('passport/index/index')->assign($view);
    }

    /**
     * 管理菜单
     * @return json
     */
    public function appmenu(){
        $app_menu    =  [];
        $miniapp_dir = 'system';
        if ($this->member_miniapp_id) {
            if($this->member_miniapp->is_lock == 0){
                $miniapp = SystemMiniapp::field('miniapp_dir,types,is_wechat_pay,is_alipay_pay,is_openapp')->where(['id' => $this->member_miniapp->miniapp_id])->find();
                if($miniapp){
                    $miniapp_dir = $miniapp->miniapp_dir;
                    if($this->user->parent_id == 0 && $this->user->lock_config == 0){
                        $app_menu['name'] = '应用管理';
                        $app_menu['icon'] = 'yingyongyuanma';
                        if(!empty($this->member_miniapp->mp_appid) && !empty($this->member_miniapp->miniapp_appid)){
                            if ($miniapp->types == 'mp' || $miniapp->types == 'mp_program' || $miniapp->types == 'mp_program_app'){
                                $app_menu['nav'][] = ['name' => '自定义菜单','url' => url('system/passport.official/index'),'icon' => 'news_icon'];
                            }
                            $app_menu['nav'][] = ['name' => '自动回复','url' => url('system/passport.keyword/index'),'icon' => 'zhuanzhang'];
                        }                      
                        $app_menu['nav'][] = ['name' => '关于应用','url' => url('system/passport.setting/index'),'icon' => 'information'];
                    }
                }
            }
        }
        $menu = AppConfig::menu($miniapp_dir);
        if(!empty($app_menu)){
            array_push($menu,$app_menu);
        }
        return json($menu);
    }

    /**
     * 修改密码
     * @access public
     */
    public function password(){
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if(request()->isAjax()){
            $data = [
                'id'               => $this->user->id,
                'login_password'   => Request::param('safe_password'),
                'password'         => Request::param('password/s'),
                'password_confirm' => Request::param('repassword/s'),
            ];
            $validate = $this->validate($data,'Member.password');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            //验证密码
            if(!password_verify(md5($data['login_password']),$this->user->safe_password)) {
                return enjson(0,"安全密码错误");
            }
            $result = SystemMember::upDatePasspowrd($this->user->id,$data['password']);
            if($result){
                return enjson(200,"修改成功",['url' => url('system/passport.login/logout')]);
            }
            return enjson(0);
        }else{
            return view();
        }
    }
    
    /**
     * 切换当前管理的应用
     * @param int 当前应用APP的ID
     * */
    public function changeApp(int $id){
        $condition = [];
        $condition['is_lock'] = 0;
        if($this->user->parent_id){
            $condition['id'] = $this->user->bind_member_miniapp_id;
        }else{
            $condition['id']        = $id;
            $condition['member_id'] = $this->user->id;
        }
        $rel = SystemMemberMiniapp::where($condition)->find();
        if(empty($rel)){
            return json(['code'=>0,'msg'=>'操作失败']);
        }else{
            if($rel['is_lock'] == 1){
                return json(['code'=>0,'msg'=>'应用已被锁定,禁止管理']);
            }
            $param = [
                'member_id'         => $rel['member_id'],
                'miniapp_id'        => $rel['miniapp_id'],
                'member_miniapp_id' => $rel['id'],
            ];
            Passport::setMiniapp($param);
            return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/passport.index/index')]);
        }
    }

   /**
     * 用户已经开通的应用（弹窗选项）
     * @access public
     */
    public function selectMemberMiniapp(){
        if($this->user->parent_id){
            $this->error('你不是创始人,禁止访问.');
        }
        $view['input'] = Request::param('input/s');
        $apps  = SystemMemberMiniapp::where(['member_id'=>$this->user->id])->order('id desc')->select();
        foreach ($apps as $key => $value) {
            $apps[$key] = $value;
            switch ($value->miniapp->types) {
                case 'mp':
                    $head_img = $value->mp_head_img;
                    break;
                case 'program':
                    $head_img = $value->miniapp_head_img;
                    break;
                case 'app':
                    $head_img = $value->head_img;
                    break;
                default:
                    $head_img = empty($value->mp_head_img) ? $value->miniapp_head_img : $value->mp_head_img;
                    break;
            }
            $apps[$key]['logo'] = empty($head_img) ? "/static/{$value->miniapp->miniapp_dir}/logo.png" : $head_img;
        }
        $view['list'] = $apps;
        return view()->assign($view);
    } 
}
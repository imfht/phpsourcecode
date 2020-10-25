<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用设置
 */
namespace app\system\controller\passport;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemMemberMiniappToken;
use app\common\model\SystemMemberMiniappOrder;
use app\common\model\SystemMemberWechatTpl;
use app\common\model\SystemMemberMiniappCode;
use app\common\facade\WechatMp;
use app\common\facade\WechatProgram;
use think\facade\Request;
use Exception;

class Setting extends Common{

    public function initialize(){
        parent::initialize();
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if($this->user->parent_id){
            $this->error('仅创始人有权限访问');
        }
        if(!$this->member_miniapp_id){
            $this->error('未找到所属应用,请先开通应用.');
        }
        $this->assign('pathMaps', [['name'=>$this->member_miniapp->appname,'url'=>'javascript:;'],['name'=>'应用管理','url'=>url("system/passport.setting/index")]]);
    }

    /**
     * 列表
     * @access public
     */
    public function index(){
        if(empty($this->member_miniapp->head_img)){
            switch ($this->member_miniapp->miniapp->types) {
                case 'mp':
                    $head_img = $this->member_miniapp->mp_head_img;
                    break;
                case 'program':
                    $head_img = $this->member_miniapp->miniapp_head_img;
                    break;
                case 'app':
                    $head_img = $this->member_miniapp->head_img;
                    break;
                default:
                    $head_img = empty($this->member_miniapp->mp_head_img) ? $this->member_miniapp->mp_head_img : $this->member_miniapp->miniapp_head_img;
                    break;
            }
        }else{
            $head_img = $this->member_miniapp->head_img;;
        }
        $view['logo']    = empty($head_img) ? "/static/{$this->member_miniapp->miniapp->miniapp_dir}/logo.png" : $head_img;
        $view['miniapp'] = SystemMemberMiniappOrder::where(['member_id' => $this->user->id,'miniapp_id' => $this->member_miniapp->miniapp->id])->count(); 
        /**
         * 开放平台信息小程序管理 
         * is_commit = 1、2、3、4
         * 1、基础信息设置
         * 2、上传代码
         * 3、提交审核
         * 4、发布小程序
         */
        $view['is_authorize'] = 0;
        $view['code']['is_commit'] = 1;
        $view['code']['state']     = 0;
        if(!$this->member_miniapp->miniapp->is_openapp && $this->member_miniapp->miniapp_appid){
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if($miniapp){
                $view['is_authorize']      = 1;
                $view['auditid']['status'] = -1;
                $view['code'] = SystemMemberMiniappCode::where(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' => $this->user->id])->find(); //查询状态
                if($view['code']){
                    //查询审核状态 is_commit = 3 && state = 1
                    if($view['code']['is_commit'] == 3 && $view['code']['state'] == 1){
                        $rel = $miniapp->code->getLatestAuditStatus();
                        if($rel['errcode']  == 0){
                            $view['auditid'] = $rel;
                            if($rel['status'] == 1){
                                $view['code']->state = 0;
                                $view['code']->is_commit = 2;
                                $view['code']->save();
                            }
                        }
                    }
                    //已发布的代码 is_commit = 4 && state = 0
                    $view['update_var'] = 0;
                    if($view['code']['is_commit'] == 4 && $view['code']['state'] == 0){
                        $var = SystemMemberMiniappOrder::where(['id' => $this->member_miniapp->miniapp_order_id])->field('update_var')->find();
                        if($this->member_miniapp->miniapp->template_id > $var->update_var){
                            $view['update_var']        = 1;
                            $view['code']['is_commit'] = 2;
                        }
                    }
                    if(!file_exists(PATH_PUBLIC.$view['code']['trial_qrcode'])){
                        $view['code']['trial_qrcode'] = '';
                    }
                }else{
                    $view['code']['state'] = 0;
                }
            }
        }
        return view()->assign($view);
    }
    

    /**
     * 编辑授权
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'             => Request::param('id/d'),
                'member_id'      => $this->user['id'],
                'appname'        => Request::param('appname/s'),
                'is_psp'         => Request::param('is_psp/d'),
                'psp_appid'      => Request::param('psp_appid/s'),
                'miniapp_appid'  => Request::param('miniapp_appid/s'),
                'miniapp_secret' => Request::param('miniapp_secret/s'),
                'mp_appid'       => Request::param('mp_appid/s'),
                'mp_secret'      => Request::param('mp_secret/s'),
                'mp_token'       => Request::param('mp_token/s'),
                'mp_aes_key'     => Request::param('mp_aes_key/s'),
                'navbar_color'   => Request::param('navbar_color/s'),
                'navbar_style'   => Request::param('navbar_style/s'),
            ];
            if($this->member_miniapp->miniapp->is_openapp){
                switch ($this->member_miniapp->miniapp->types) {
                    case 'mp':
                        $validate = $this->validate($data,'miniapp.editOfficia');
                        break;
                    case 'program':
                        $validate = $this->validate($data,'miniapp.editProgram');
                        break;
                    case 'app':
                        $validate = $this->validate($data,'miniapp.editApp');
                        break;
                    default:
                        $validate = $this->validate($data,'miniapp.editMiniapp');
                        break;
                }
            }else{
                $validate = $this->validate($data,'miniapp.editApp');
            }            
            if(true !== $validate){
                return enjson(0,$validate);
            }
            if($data['is_psp'] == 1){
                if(empty($data['psp_appid'])){
                    return enjson(0,'服务商支付开启的,必须填写服务商AppID');
                }
            }else{
                $data['psp_appid'] = '0';
            }
            $result  = SystemMemberMiniapp::editer($data);
            if($result){
                return enjson(200,'成功',['url' => url('system/passport.setting/edit')]);
            }
            return enjson(0);
        }else{
            return view();
        }
    }

    /**
     * 微信支付
     * @return void
     */
    public function wepay(){
        if($this->member_miniapp->miniapp->is_wechat_pay == 0){
            return $this->error("应用没有微信支付服务");
        }
        if(request()->isPost()){
            $rules = [
                'id'        => Request::param('id/d'),
                'mch_id'    => Request::param('mch_id/s'),
                'key'       => Request::param('key/s'),
                'cert_path' => Request::param('cert_path/s'),
                'key_path'  => Request::param('key_path/s'),
            ];
            $validate = $this->validate($rules,'Payment.wechat');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->member_miniapp_id])->find();
            $config = json_encode(['mch_id' => $rules['mch_id'],'key'=>$rules['key'],'cert_path' => $rules['cert_path'],'key_path' => $rules['key_path']]);
            if($rel){
                $rel->config      = $config;
                $rel->update_time = time();    
                $result = $rel->save();
            }else{
                $data['config']            = $config;
                $data['update_time']       = time();  
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['member_id']         = $this->user->id;
                $data['apiname']           = 'wepay';
                $result  = SystemMemberPayment::insert($data);
            }
            if($result){
                return enjson(200,'成功',['url' => url('system/passport.setting/wepay')]);
            }
            return enjson(0);
        }else{
            $rel = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->member_miniapp_id])->find();
            $view['config'] = json_decode($rel['config'],true);
            return view()->assign($view);
        }
    }

    /**
     * 支付宝支付
     * @return void
     */
    public function alipay(){
        if($this->member_miniapp->miniapp->is_alipay_pay == 0){
            return $this->error("应用没有支付宝支付服务");
        }
        if(request()->isPost()){
            $rules = [
                'id'          => Request::param('id/d'),
                'app_id'      => Request::param('app_id/s'),
                'public_key'  => Request::param('public_key/s'),
                'private_key' => Request::param('private_key/s'),
            ];
            $validate = $this->validate($rules,'Payment.alipay');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = SystemMemberPayment::where(['apiname'=>'alipay','member_miniapp_id'=>$this->member_miniapp_id])->find();
            $config = json_encode(['app_id' => $rules['app_id'],'public_key'=>$rules['public_key'],'private_key' => $rules['private_key']]);
            if($rel){
                $rel->config      = $config;
                $rel->update_time = time();    
                $result = $rel->save();
            }else{
                $data['config']            = $config;
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['member_id']         = $this->user->id;
                $data['apiname']           = 'alipay';
                $data['update_time']       = time();  
                $result  = SystemMemberPayment::insert($data);
            }
            if($result){
                return enjson(200,'成功',['url' => url('system/passport.setting/alipay')]);
            }
            return enjson(0);
        }else{
            $rel = SystemMemberPayment::where(['apiname'=>'alipay','member_miniapp_id'=>$this->member_miniapp_id])->find();
            $view['config'] = json_decode($rel['config'],true);
            return view()->assign($view);
        }
    }

    //微信模板消息
    public function tplmsg(){
        if(request()->isPost()){
            $rules = [
                'tplmsg_common_wechat' => $this->request->param('tplmsg_common_wechat/s'),
                'tplmsg_common_app'    => $this->request->param('tplmsg_common_app/s'),
            ];
            $rel = SystemMemberWechatTpl::where(['member_miniapp_id'=>$this->member_miniapp_id])->find();
            if($rel){
                $rel->tplmsg_common_wechat  = $rules['tplmsg_common_wechat'];
                $rel->tplmsg_common_app     = $rules['tplmsg_common_app'];
                $result = $rel->save();
            }else{
                $result = SystemMemberWechatTpl::create(['member_miniapp_id'=>$this->member_miniapp_id,'tplmsg_common_wechat' => $rules['tplmsg_common_wechat'],'tplmsg_common_app' => $rules['tplmsg_common_app']]);
            }
            if($result){
                return enjson(200,'成功',['url' => url('system/passport.setting/tplmsg')]);
            }
            return enjson(0);
        }else{
            $view['info'] = SystemMemberWechatTpl::where(['member_miniapp_id'=>$this->member_miniapp_id])->find();
            return view()->assign($view);
        }
    }

    /**
     * 以下部分是微信小程序官方操作
     * ###########################################
     * 发起授权
     * @return json
     */
    public function pushAuth(string $types){
        //在此查询看看当前应用是公众号还是小程序
        if($this->member_miniapp->is_lock == 1){
            $this->error('当前应用已停止服务');
        }
        if($this->member_miniapp->miniapp->is_openapp){
            $this->error('应用不支持扫码授权,请手动配置',url('system/passport.setting/edit'));
        }
        //查询授权是否过期
        $appid  = $types == 'mp' ? $this->member_miniapp->mp_appid : $this->member_miniapp->miniapp_appid;
        if(!empty($appid)){
            try {
                $assess = SystemMemberMiniappToken::accessToken($this->member_miniapp_id,$appid);
            }catch (Exception $e) {
                $this->error($e->getMessage());
            }
            if ($assess) {
                $url = $types == 'mp' ? url('system/passport.official/index'):url('system/passport.miniapp/edit');
                $this->success('授权未过期,请10分钟后再试。',$url);
            }
        }
        try {
            $url = WechatMp::openConfig()->getPreAuthorizationUrl(url('system/passport.setting/authCallback',['id' => $this->member_miniapp_id],true,true));
        }catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return redirect($url); 
    }
       
    /**
     * 微信开放平台推送车票(1次/10分钟)
     * 有了车票要保存下来,获取授权时要用
     * @return json
     */
    public function authCallback(){
        //在此查询看看当前应用是公众号还是小程序
        if($this->member_miniapp->is_lock == 1){
            $this->error('当前应用已停止服务');
        }
        if($this->member_miniapp->miniapp->is_openapp){
            $this->error('应用不支持扫码授权,请手动配置',url('system/passport.setting/edit'));
        }
        if(Request::get('state/s') == 'STATE'){
            $this->error('您禁止了微信授权,所以暂时无权创建应用',url('system/passport.setting/index'));
        }
        $openPlatform = WechatMp::openConfig();
        if(empty($openPlatform)){
            $this->error('应用未配置开放平台授权信息');
        }
        try {
            $appinfo = $openPlatform->handleAuthorize();
        }catch (Exception $e) {
            $this->error($e->getMessage());
        }
        if(!empty($appinfo['errcode'])){
            $this->error($appinfo['errmsg']);
        }
        $appid = $appinfo['authorization_info']['authorizer_appid']; 
        //根据凭证获取的应用信息
        $miniProgram = $openPlatform->getAuthorizer($appid);
        if(empty($miniProgram['authorizer_info'])){
            $this->error('授权信息读取失败');
        }
        $head_img  = $miniProgram['authorizer_info']['head_img'] ?? '';
        if(empty($miniProgram['authorizer_info']['MiniProgramInfo'])){
            if($miniProgram['authorizer_info']['service_type_info']['id'] != 2){
                $this->error('不支持订阅号');
            }
            if($miniProgram['authorizer_info']['verify_type_info']['id'] < 0){
                $this->error('未通过微信认证的服务号禁止接入');
            }
            $app_data['mp_appid']           = $appid;  //公众号
            $app_data['mp_head_img']        = $head_img;
            $app_data['mp_qrcode_url']      = $miniProgram['authorizer_info']['qrcode_url'];
        }else{
            if($miniProgram['authorizer_info']['verify_type_info']['id'] < 0){
                $this->error('未通过微信认证的小程序禁止接入');
            }
            $app_data['miniapp_appid']      = $appid;  //小程序
            $app_data['miniapp_head_img']   = $head_img;
            $app_data['miniapp_qrcode_url'] = $miniProgram['authorizer_info']['qrcode_url'];
        }
        $app_data['is_open'] = 1;
        SystemMemberMiniapp::where(['id' => $this->member_miniapp_id])->update($app_data);
        //更新授权信息
        $at['member_miniapp_id'] = $this->member_miniapp_id;
        $at['appid']             = $appid;
        $at['access_token']      = $appinfo['authorization_info']['authorizer_access_token'];
        $at['expires_in']        = $appinfo['authorization_info']['expires_in'];
        $at['refresh_token']     = $appinfo['authorization_info']['authorizer_refresh_token'];
        SystemMemberMiniappToken::edit($at);
        //设置授权域名
        if(!empty($miniProgram['authorizer_info']['MiniProgramInfo'])){
            $url['action'] = 'set';
            $url['requestdomain']   = ['https://res.'.Request::rootDomain(),'https://'.$this->web->url];
            $url['wsrequestdomain'] = ['wss://res.'.Request::rootDomain(),'wss://'.$this->web->url];
            $url['uploaddomain']    = $url['requestdomain'];
            $url['downloaddomain']  = $url['requestdomain'];
            $domain = WechatProgram::isTypes($this->member_miniapp_id)->domain;
            $domain->modify($url);
            $domain->setWebviewDomain([Request::scheme().'://'.Request::rootDomain()]);    //设置业务域名
        }
        return redirect('system/passport.setting/index'); 
    } 
}
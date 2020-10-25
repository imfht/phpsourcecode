<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 站点设置
 */
namespace app\system\controller\admin;
use app\common\controller\Admin;
use app\common\model\SystemApis;
use app\common\model\SystemWeb;
use think\facade\Request;
use think\Validate;

class Setting extends Admin{

    public function initialize() {
        parent::initialize();
    }

    /**
     * 站点管理
     */
    public function webConfig() {
        if(request()->isAjax()){
            $data = [
                'name'        => $this->request->param('name/s'),
                'title'       => $this->request->param('title/s'),
                'url'         => $this->request->param('url/s'),
                'logo'        => $this->request->param('logo/s'),
                'keywords'    => $this->request->param('keywords/s'),
                'description' => $this->request->param('description/s'),
                'icp'         => $this->request->param('icp/s'),
                'contacts'    => $this->request->param('contacts/s'),
                'address'     => $this->request->param('address/s'),
            ];
            $validate = $this->validate($data,'config.web');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $config = SystemWeb::where(['id' => 1])->find();
            if($config){
                $result = SystemWeb::where(['id' => 1])->update($data);
            }else{
                $result = SystemWeb::create($data);
            }
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/webConfig')]);
            }
            return enjson(0);
        }else{
            $view['pathMaps']  = [['name' => '站点配置','url' => url("system/admin.setting/webConfig")]];
            $view['info']  = SystemWeb::config();
            return view()->assign($view);
        }
    }

    /**
     * 微信支付配置
     */
    public function wechatPay(){
        $apiname = 'wepay';
        if(request()->isPost()){    
            $data = [
                'app_id'    => $this->request->param('app_id/s'),
                'mch_id'    => $this->request->param('mch_id/s'),
                'key'       => $this->request->param('key/s'),
                'cert_path' => $this->request->param('cert_path/s'),
                'key_path'  => $this->request->param('key_path/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/wechatPay')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '微信支付配置','url' => url("system/admin.setting/wechatPay")]];
            return view()->assign($view); 
        }
    }

     /**
     * 微信服务号
     */
    public function wechatAccount() {
        $apiname = 'wechataccount';
        if(request()->isPost()){
            $data = [
                'qrcode_login' => $this->request->param('qrcode_login/d',0),
                'qrcode'       => $this->request->param('qrcode/s'),
                'app_id'       => $this->request->param('app_id/s'),
                'secret'       => $this->request->param('secret/s'),
                'token'        => $this->request->param('token/s'),
                'aes_key'      => $this->request->param('aes_key/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/wechatAccount')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '微信服务号','url' => url("system/admin.setting/wechatAccount")]];
            return view()->assign($view); 
        }
    }
    
    /**
     * 云市场
     */
    public function wechatCloud() {
        $apiname = 'wechatcloud';
        if(request()->isPost()){
            $data = [
                'app_id'     => $this->request->param('app_id/s'),
                'secret_id'  => $this->request->param('secret_id/s'),
                'secret_key' => $this->request->param('secret_key/s'),
                'encry_key'  => $this->request->param('encry_key/s'),
                'token'      => $this->request->param('token/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/wechatCloud')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '云市场','url' => url("system/admin.setting/wechatCloud")]];
            return view()->assign($view);
        }
    }


    /**
     * 微信开放平台
     */
    public function wechatOpen() {
        $apiname = 'wechatopen';
        if(request()->isPost()){    
            $data = [
                'app_id'  => $this->request->param('app_id/s'),
                'secret'  => $this->request->param('secret/s'),
                'token'   => $this->request->param('token/s'),
                'aes_key' => $this->request->param('aes_key/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/wechatOpen')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '开放平台配置','url' => url("system/admin.setting/wechatOpen")]];
            return view()->assign($view); 
        }
    }


    /**
     * 阿里短信接口配置
     */
    public function aliSms() {
        $apiname = 'alisms';
        if(request()->isPost()){
            $data = [
                'aes_key'   => $this->request->param('aes_key/s'),
                'secret'    => $this->request->param('secret/s'),
                'sign_name' => $this->request->param('sign_name/s'),
                'tpl_id'    => $this->request->param('tpl_id/s'),
                'price'     => $this->request->param('price/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/aliSms')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '短信模板','url' => url("system/admin.setting/aliSms")]];
            return view()->assign($view); 
        }
    }

    /**
     * 阿里云市场配置
     */
    public function aliApi() {
        $apiname = 'aliapi';
        if(request()->isPost()){
            $data = [
                'appcode' => Request::param('appcode/s'),
                'price'   => $this->request->param('price/s'),
            ];
            $validate = $this->validate($data,'Config.'.$apiname);
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/aliApi')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => '阿里云市场配置','url' => url("system/admin.setting/aliApi")]];
            return view()->assign($view); 
        }
    }

    /**
     * 远程附件
     */
    public function aliOss() {
        $apiname = 'alioss';
        if(request()->isPost()){
            $data = [
                'upload_driver' => $this->request->param('upload_driver','local'),
                'upload_exts'   => $this->request->param('upload_exts','jpeg,jpg,gif,png'),
                'domain'        => $this->request->param('domain',Request::root(true).'/'),
                'access_id'     => $this->request->param('access_id'),
                'secret_key'    => $this->request->param('secret_key'),
                'bucket'        => $this->request->param('bucket'),
                'city'          => $this->request->param('city'),
                'is_internal'   => $this->request->param('is_internal/d',0),
            ];
            $rules   = ['upload_exts' => 'require','domain' => 'require',];
            $message = ['upload_exts' => '文件类型必须填写','domain' => '附件域名必须填写',];
            if($data['upload_driver'] == 'oss'){
                $rules['access_id']     = 'require|alphaNum';
                $rules['secret_key']    = 'require|alphaNum';
                $rules['bucket']        = 'require|alphaNum';
                $rules['city']          = 'require|chs';
            }
            $validate = Validate::make($rules,$message);
            if (!$validate->check($data)) {
                return enjson(0,$validate->getError());
            }
            $result  = SystemApis::edit($apiname,$data);
            if($result){
                return enjson(200,'成功',['url'=>url('system/admin.setting/aliOss')]);
            }
            return enjson(0);
        }else{
            $view['info']      = SystemApis::config($apiname);
            $view['pathMaps']  = [['name' => 'OSS储存','url' => url("system/admin.setting/aliOss")]];
            return view()->assign($view); 
        }
    }
}
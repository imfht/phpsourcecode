<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应答服务
 */
namespace app\system\controller\passport;
use app\common\model\SystemMemberKeyword;
use app\common\facade\WechatMp;
use app\common\facade\WechatProgram;
use think\facade\Config;
use Yurun\Util\HttpRequest;
use util\Util;
use Exception;

class Keyword extends Common{

    public function initialize() {
        parent::initialize();
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if($this->user->parent_id){
            $this->error('无权限访问,只有创始人身份才允许使用。');
        }
    }

    /**
     * 自动切换
     */
    public function index(){        
        if(empty($this->member_miniapp->mp_appid) && empty($this->member_miniapp->miniapp_appid)){
            $this->error('请先授权公众号或小程序',url('passport.setting/index'),'去授权应用');
        }
        if($this->member_miniapp->miniapp->types == 'program' || $this->member_miniapp->miniapp->types == 'mp_program'){
            return redirect('passport.keyword/miniapp');
        }else{
            return redirect('passport.keyword/official');
        }
    }
    
    /**
     * 公众号应答 
     */
    public function official(){
        $tabs = [];
        if ($this->member_miniapp->mp_appid){
            $tabs[] = ['name' =>'公众号应答','url' =>url('passport.keyword/official')];
        }
        if ($this->member_miniapp->miniapp_appid){
            $tabs[] = ['name' =>'小程序应答','url' =>url('passport.keyword/miniapp')];
        }
        $this->assign('tabs',$tabs);
        $this->assign('pathMaps', [['name'=>'应答服务','url'=>'javascript::'],['name'=>'公众号应答','url'=>url('passport.keyword/official')]]);
        $this->assign('list',SystemMemberKeyword::where(['member_miniapp_id' => $this->member_miniapp_id,'is_miniapp' => 0])->order('id desc')->paginate(20));
        return view();
    }


     /**
     * 弹出选择公众号
     */
    public function selectOfficial(){
        $view['input'] = $this->request->param('input');
        $view['list'] = SystemMemberKeyword::where(['member_miniapp_id' => $this->member_miniapp_id,'is_miniapp' => 0])->order('id desc')->paginate(20);
        return view()->assign($view);;
    }

    /**
     * 文字应答
     */
    public function text(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            $data = [
                'id'         => $id,
                'keyword'    => $this->request->param('keyword/s'),
                'content'    => $this->request->param('content/s'),
            ];
            $validate = $this->validate($data,'Keyword.text');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            if(empty($info)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['type']              = 'text';
                $data['is_miniapp']        = 0;
                $result = SystemMemberKeyword::create($data);
            }else{
                $result = SystemMemberKeyword::update($data);
            }
            if($result){
                return enjson(200);
            }else{
                return enjson(0);
            } 
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }

    /**
     * 图片应答
     */
    public function image(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            try {
                $data = [
                    'id'         => $id,
                    'keyword'    => $this->request->param('keyword/s'),
                    'image'      => $this->request->param('image/s'),
                ];
                $validate = $this->validate($data,'Keyword.image');
                if(true !== $validate){
                    return json(['code'=>0,'msg'=>$validate]);
                }
                $official  = WechatMp::isTypes($this->member_miniapp_id);
                if(!$official){
                    return enjson(0,'微信认证失败,请确认应用已授权.');
                }
                //上传图片到微信服务器,并返回mediaId
                $config = config::get('upload.');
                $thumb_img = substr(parse_url($data['image'])['path'],1);
                if(empty($info) || $info->image != $data['image']){
                    if($config['upload_driver'] == 'oss'){
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                        if (!file_exists($thumb_path)) {
                            if(Util::mkdir(dirname($thumb_path))){
                                $http = new HttpRequest;
                                $http->download($thumb_path,$data['image']);
                            }
                        }
                    }else{
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                    }
                    if(file_exists($thumb_path)){
                        $thumb = $official->material->uploadThumb($thumb_path);
                        if(empty($thumb['media_id'])){
                            return enjson(0,'上传资源到微信服务器失败');
                        }
                        $data['media_id'] = $thumb['media_id'];
                        $data['media'] = json_encode($thumb);
                    }
                }
                if(empty($info)){
                    $data['member_miniapp_id'] = $this->member_miniapp_id;
                    $data['type']              = 'image';
                    $data['is_miniapp']        = 0;
                    $result = SystemMemberKeyword::create($data);
                }else{
                    $result = SystemMemberKeyword::update($data);
                }
                if($result){
                    return enjson(200,'成功',['url' => url('system/passport.keyword/official')]);
                }else{
                    return enjson(0);
                }
            } catch (Exception $e) {
                return enjson(0,$e->getMessage());
            }
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }

    /**
     * 链接应答
     */
    public function link(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            try {
                $data = [
                    'id'         => $id,
                    'media_id'   => $this->request->param('media_id/s'),
                    'keyword'    => $this->request->param('keyword/s'),
                    'image'      => $this->request->param('image/s'),
                    'title'      => $this->request->param('title/s'),
                    'url'        => $this->request->param('url/s'),
                    'content'    => $this->request->param('content/s'),
                ];
                $validate = $this->validate($data,'Keyword.link');
                if(true !== $validate){
                    return json(['code'=>0,'msg'=>$validate]);
                }
                $official  = WechatMp::isTypes($this->member_miniapp_id);
                if(!$official){
                    return enjson(0,'微信认证失败,请确认应用已授权.');
                }
                //上传图片到微信服务器,并返回mediaId
                $config = config::get('upload.');
                $thumb_img = substr(parse_url($data['image'])['path'],1);
                if(empty($info) || $info->image != $data['image']){
                    if($config['upload_driver'] == 'oss'){
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                        if (!file_exists($thumb_path)) {
                            if(Util::mkdir(dirname($thumb_path))){
                                $http = new HttpRequest;
                                $http->download($thumb_path,$data['image']);
                            }
                        }
                    }else{
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                    }
                    if(file_exists($thumb_path)){
                        $thumb = $official->material->uploadThumb($thumb_path);
                        if(empty($thumb['media_id'])){
                            return enjson(0,'上传资源到微信服务器失败');
                        }
                        $data['media_id'] = $thumb['media_id'];
                        $data['media'] = json_encode($thumb);
                    }
                }
                if(empty($info)){
                    $data['member_miniapp_id'] = $this->member_miniapp_id;
                    $data['type']              = 'link';
                    $data['is_miniapp']        = 0;
                    $result = SystemMemberKeyword::create($data);
                }else{
                    $result = SystemMemberKeyword::update($data);
                }
                if($result){
                    return enjson(200,'成功',['url' => url('system/passport.keyword/official')]);
                }else{
                    return enjson(0);
                }
            } catch (Exception $e) {
                return enjson(0,$e->getMessage());
            }
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }  
    
    /**
     * 微信图文素材
     */
    public function media(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            $data = [
                'id'         => $id,
                'keyword'    => $this->request->param('keyword/s'),
                'media_id'   => $this->request->param('media_id/s'),
            ];
            $validate = $this->validate($data,'Keyword.media');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            if(empty($info)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['type']              = 'media';
                $data['is_miniapp']        = 0;
                $result = SystemMemberKeyword::create($data);
            }else{
                $result = SystemMemberKeyword::update($data);
            }
            if($result){
                return json(['code'=>200,'msg'=>'修改成功','url' => url('system/passport.keyword/official')]);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            } 
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }    

    /**
     * 小程序文字应答
     */
    public function miniappText(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            $data = [
                'id'         => $id,
                'keyword'    => $this->request->param('keyword/s'),
                'content'    => $this->request->param('content/s'),
            ];
            $validate = $this->validate($data,'Keyword.text');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            if(empty($info)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['type']              = 'text';
                $data['is_miniapp']        = 1;
                $result = SystemMemberKeyword::create($data);
            }else{
                $result = SystemMemberKeyword::update($data);
            }
            if($result){
                return json(['code'=>200,'msg'=>'修改成功','url' => url('system/passport.keyword/miniapp')]);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            } 
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }

    /**
     * 小程序图片应答
     */
    public function miniappImage(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            try {
                $data = [
                    'id'         => $id,
                    'media_id'   => $this->request->param('media_id/s'),
                    'keyword'    => $this->request->param('keyword/s'),
                    'image'      => $this->request->param('image/s'),
                ];
                $validate = $this->validate($data,'Keyword.image');
                if(true !== $validate){
                    return json(['code'=>0,'msg'=>$validate]);
                }
                $program  = WechatProgram::isTypes($this->member_miniapp_id);
                if(!$program){
                    return enjson(0,'微信认证失败,请确认应用已授权.');
                }
                //上传图片到微信服务器,并返回mediaId
                $config = config::get('upload.');
                $thumb_img = substr(parse_url($data['image'])['path'],1);
                if(empty($info) || $info->image != $data['image']){
                    if($config['upload_driver'] == 'oss'){
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                        if (!file_exists($thumb_path)) {
                            if(Util::mkdir(dirname($thumb_path))){
                                $http = new HttpRequest;
                                $http->download($thumb_path,$data['image']);
                            }
                        }
                    }else{
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                    }
                    if(file_exists($thumb_path)){
                        $thumb = $program->media->uploadImage($thumb_path);
                        if(empty($thumb['media_id'])){
                            return enjson(0,'上传资源到微信服务器失败');
                        }
                        $data['media_id'] = $thumb['media_id'];
                        $data['media'] = json_encode($thumb);
                    }
                }
                if(empty($info)){
                    $data['member_miniapp_id'] = $this->member_miniapp_id;
                    $data['type']              = 'image';
                    $data['is_miniapp']        = 1;
                    $result = SystemMemberKeyword::create($data);
                }else{
                    $result = SystemMemberKeyword::update($data);
                }
                if($result){
                    return enjson(200,'成功',['url' => url('system/passport.keyword/miniapp')]);
                }else{
                    return enjson(0);
                }
            } catch (Exception $e) {
                return enjson(0,$e->getMessage());
            }
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }

    /**
     * 链接应答
     */
    public function  miniappLink(){
        $id = $this->request->param('id/d',0);
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $info = SystemMemberKeyword::where($condition)->find();
        if(request()->isPost()){
            try {
                $data = [
                    'id'         => $id,
                    'media_id'   => $this->request->param('media_id/s'),
                    'keyword'    => $this->request->param('keyword/s'),
                    'image'      => $this->request->param('image/s'),
                    'title'      => $this->request->param('title/s'),
                    'url'        => $this->request->param('url/s'),
                    'content'    => $this->request->param('content/s'),
                ];
                $validate = $this->validate($data,'Keyword.link');
                if(true !== $validate){
                    return json(['code'=>0,'msg'=>$validate]);
                }
                $program  = WechatProgram::isTypes($this->member_miniapp_id);
                if(!$program){
                    return enjson(0,'微信认证失败,请确认应用已授权.');
                }
                //上传图片到微信服务器,并返回mediaId
                $config = config::get('upload.');
                $thumb_img = substr(parse_url($data['image'])['path'],1);
                if(empty($info) || $info->image != $data['image']){
                    if($config['upload_driver'] == 'oss'){
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                        if (!file_exists($thumb_path)) {
                            if(Util::mkdir(dirname($thumb_path))){
                                $http = new HttpRequest;
                                $http->download($thumb_path,$data['image']);
                            }
                        }
                    }else{
                        $thumb_path = PATH_PUBLIC.$thumb_img;
                    }
                    if(file_exists($thumb_path)){
                        $thumb = $program->media->uploadImage($thumb_path);
                        if(empty($thumb['media_id'])){
                            return enjson(0,'上传资源到微信服务器失败');
                        }
                        $data['media_id'] = $thumb['media_id'];
                        $data['media'] = json_encode($thumb);
                    }
                }
                if(empty($info)){
                    $data['member_miniapp_id'] = $this->member_miniapp_id;
                    $data['type']              = 'link';
                    $data['is_miniapp']        = 1;
                    $result = SystemMemberKeyword::create($data);
                }else{
                    $result = SystemMemberKeyword::update($data);
                }
                if($result){
                    return enjson(200,'成功',['url' => url('system/passport.keyword/miniapp')]);
                }else{
                    return enjson(0);
                }
            } catch (Exception $e) {
                return enjson(0,$e->getMessage());
            }
        }else{
            $view['info'] =  $info;
            return view()->assign($view);;
        }
    }  

     /**
     * 小程序 
     */
    public function miniapp(){
        $tabs = [];
        if ($this->member_miniapp->mp_appid){
            $tabs[] = ['name' =>'公众号应答','url' =>url('passport.keyword/official')];
        }
        if ($this->member_miniapp->miniapp_appid){
            $tabs[] = ['name' =>'小程序应答','url' =>url('passport.keyword/miniapp')];
        }
        $this->assign('tabs',$tabs);
        $this->assign('pathMaps', [['name'=>'应答服务','url'=>'javascript::'],['name'=>'小程序应答','url'=>url('passport.keyword/miniapp')]]);
        $this->assign('list',SystemMemberKeyword::where(['member_miniapp_id' => $this->member_miniapp_id,'is_miniapp' => 1])->order('id desc')->paginate(20));
        return view();
    }

     /**
     * 弹出小程序 
     */
    public function selectMiniapp(){
        $view['input'] = $this->request->param('input');
        $view['list']  = SystemMemberKeyword::where(['member_miniapp_id' => $this->member_miniapp_id,'is_miniapp' => 1])->order('id desc')->paginate(20);
        return view()->assign($view);;
    }

    /**
     * 关键字重复
     * @param integer $id
     * @return void
     */
    public function keyword(){
        $condition[] = ['member_miniapp_id','=',$this->member_miniapp_id];
        $condition[] = ['keyword','=',$this->request->param('param/s')];
        $condition[] = ['id','<>',$this->request->param('id/d',0)];
        $result = SystemMemberKeyword::where($condition)->count();
        if($result){
            return json(['status'=>'n','info'=>'关键字重复']);
        }else{
            return json(['status'=>'y','info'=>'可以使用']);
        }
    }
    /**
     * 删除
     * @param integer $id 删除ID
     */
    public function delete(int $id){
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['id']                = $id;
        $result = SystemMemberKeyword::where( $condition)->delete();
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }
}
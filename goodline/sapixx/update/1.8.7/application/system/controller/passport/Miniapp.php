<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序管理
 */
namespace app\system\controller\passport;
use app\common\facade\WechatProgram;
use app\common\model\SystemMemberMiniappOrder;
use app\common\model\SystemMiniapp;
use app\common\model\SystemMemberMiniappCode;
use app\common\facade\Qrcode;
use think\facade\Request;
use app\common\facade\Upload;

class Miniapp extends Common{

    protected $program;

    public function initialize() {
        parent::initialize();
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if($this->user->parent_id){
            $this->error('无权限访问,只有创始人身份才允许使用.');
        }
        if(!$this->member_miniapp_id){
            $this->error('未找到所属应用,请先开通应用.');
        }
        if ($this->member_miniapp->miniapp->types == 'mp' || $this->member_miniapp->miniapp->types == 'app'){
            $this->error('非小程序应用',url('system/passport.setting/index'));
        }
        if($this->member_miniapp->miniapp->is_openapp){
            $this->error('非SaaS应用请自行配置应用',url('system/passport.setting/index'));
        }
        $this->program = WechatProgram::isTypes($this->member_miniapp_id);
        if(!$this->program){
            $this->error('小程序还未授权,禁止操作');
        }
    }
    
    /**
     * 提交审核
     * @access public
     */
    public function submitPass(){
        $getcate = $this->program->code->getCategory();  //小程序权限目录
        if($getcate['errcode'] == -1){
            $this->error($getcate['errmsg']);
        }
        $page = $this->program->code->getPage();  //小程序页面
        if($page['errcode'] != 0){
            $this->error($page['errmsg']);
        }
        $cate = [];
        foreach ($getcate['category_list'] as $key => $value) {
            $cate[$key]['name'] = empty($value['third_class']) ? $value['first_class'].'-'.$value['second_class'] : $value['first_class'].'-'.$value['second_class'].'-'.$value['third_class'];
            $cate[$key]['id']   = empty($value['third_id']) ? $value['first_id'].'-'.$value['second_id'] : $value['first_id'].'-'.$value['second_id'].'-'.$value['third_id'];
        }
        $view['cate'] = $cate;
        $view['page'] = $page['page_list'];
        $view['id']   = $this->member_miniapp_id;
        return view()->assign($view);
    }

    
     /**
     * 以下部分是微信小程序官方操作
     * ###########################################
     * 设置域名
     */
    public function domain(){
        if(request()->isAjax()){
            $rel = SystemMemberMiniappCode::where(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' => $this->user->id])->find();
            if(!empty($rel) && $rel->is_commit == 3 && $rel->state == 1){
                return enjson(0,'审核中小程序禁止设置业务域名'); 
            }
            $url['action'] = 'set';
            $url['requestdomain']   = ['https://res.'.Request::rootDomain(),'https://'.$this->web['url']];
            $url['wsrequestdomain'] = ['wss://res.'.Request::rootDomain(),'wss://'.$this->web['url']];
            $url['uploaddomain']    = $url['requestdomain'];
            $url['downloaddomain']  = $url['requestdomain'];
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if(!$miniapp){
                return enjson(0,'小程序还未授权,禁止操作'); 
            }
            $rel = $this->program->domain->modify($url);
            if($rel['errcode'] > 0){
                return enjson(0,'服务器域名:'.$rel['errmsg']); 
            }
            $this->program->domain->setWebviewDomain([Request::scheme().'://'.Request::rootDomain()]);  //设置业务域名
            $data['is_commit']         = 2;
            $data['member_miniapp_id'] = $this->member_miniapp_id;
            $data['member_id']         = $this->user['id'];
            SystemMemberMiniappCode::edit(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' =>$this->user->id],$data);
            return enjson(200,'设置域名成功'); 
        }
    }

    /**
     * 上传代码
     */
    public function upCode(){
        if(request()->isAjax()){
            $rel = SystemMemberMiniappCode::where(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' => $this->user->id])->find();
            if(!empty($rel) && $rel->is_commit == 3 && $rel->state == 1){
                return enjson(0,'审核中小程序禁止上传代码'); 
            }
            //读取小程序信息
            $app = SystemMiniapp::where(['id'=>$this->member_miniapp->miniapp_id,'is_lock'=>0])->find();
            if(empty($app)){
                return json(['code'=>200,'msg'=>'小程序不存在或暂停服务']);
            }
            //上传参数
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if(!$miniapp){
                return json(['code'=>0,'msg'=>'小程序还未授权,禁止操作']); 
            }
            $extJson = [
                'extAppid' =>  $this->member_miniapp->miniapp_appid,
                'ext' => [
                    "name" => $this->member_miniapp->appname,
                    "attr" => [
                        'host'    => 'https://'.Request::host(),
                        'miniapp' => $this->member_miniapp->service_id,
                    ],
                ],
                "window" => ['navigationBarTitleText' => $this->member_miniapp->appname]
            ];
            $miniapp =  $this->program->code->commit($app->template_id,json_encode($extJson),$app->version,$app->describe);
            //更新信息
            $data['is_commit']         = 3;
            $data['member_miniapp_id'] = $this->member_miniapp_id;
            $data['member_id']         = $this->user->id;
            SystemMemberMiniappCode::edit(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' =>$this->user->id],$data);
            return json(['code'=>200,'msg'=>'上传代码成功']);
        }
    }

     /**
     * 拉取二维码
     */
    public function getQrcode(){
        if(request()->isAjax()){
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if(!$miniapp){
                return enjson(0,'小程序还未授权,禁止操作');  
            }
            try {
                $qrcode = $this->program->code->getQrCode();
            } catch (\Exception $e) {
                return enjson(0,'小程序还未授权,禁止操作'); 
            }
            $data['trial_qrcode']      = Qrcode::saveQcode($qrcode,'miniapp_'.$this->member_miniapp_id);
            $data['member_miniapp_id'] = $this->member_miniapp_id;
            $data['member_id']         = $this->user->id;
            SystemMemberMiniappCode::edit(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' =>$this->user->id],$data);
            return enjson(200,'读取体验二维码成功'); 
        }else{
            return enjson(0,'读取体验二维码失败'); 
        }
    }

     /**
     * 提交审核
     */
    public function addPass(){
        if(request()->isAjax()){
            $param = [
                'cate'             => $this->request->param('cate'),
                'scene'            => $this->request->param('scene/a'),
                'other_scene_desc' => $this->request->param('other_scene_desc/s'),
                'method'           => $this->request->param('method/a'),
                'has_audit_team'   => $this->request->param('has_audit_team/d'),
                'audit_desc'       => $this->request->param('has_audit_team/d'),
                'feedback_info'    => $this->request->param('feedback_info/s'),
                'imgs'             => $this->request->param('imgs/a',[]),
            ];
            $validate = $this->validate($param,'open.addpass');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            } 
            //UGC内容生成
            $ugc = [];
            if($param['scene'][0] || ($param['scene'][0] == 0 && count($param['scene']) >= 2)){
                $ugc['scene']  = $param['scene'];    
                if(in_array(5,$param['scene']) && empty($param['other_scene_desc'])){
                    return enjson(0,'其它UGC场景的必须填写UGC说明'); 
                }
                if(in_array(5,$param['scene'])){
                    $ugc['other_scene_desc'] = $param['other_scene_desc'];
                }
                if($param['scene'][0] == 0){
                    unset($param['scene'][0]);
                    $param['scene'] = array_values($param['scene']);

                }
                $ugc['scene']          = $param['scene'];
                $ugc['method']         = $param['method'];
                $ugc['has_audit_team'] = $param['has_audit_team'];
                $ugc['audit_desc']     = $param['audit_desc'];
            }
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if(!$miniapp){
                return json(['code'=>0,'msg'=>'小程序还未授权,禁止操作']); 
            } 
            //上传小程序截图
            $preview_info = [];
            if(!empty($param['imgs'])){
                $data = Upload::uploadWechatWeapp($this->member_miniapp_id,$param['imgs']);
                if(!empty($data)){
                    $preview_info['pic_id_list'] = array_column($data,'media_id');
                }
            }
            //读取分类
            /**
             * $getcate = $this->program->code->getCategory();
             * if($getcate['errcode'] == -1){
             *     $this->error($getcate['errmsg']);
             * }
             * $itemlist = [array_merge(["address"=> $param['page'],"tag"=> $param['tag'],"title"=> $param['name']],$getcate['category_list'][$param['cate']])];
             */
            //提交审核单
            $rel = $this->program->code->submitAudit([],$preview_info,$ugc,$param['feedback_info']);
            if($rel['errcode'] != 0){
                return json(['code'=>0,'msg'=>'提交审核:'.$rel['errmsg']]);
            }
            $code_data['is_commit']         = 3;
            $code_data['state']             = 1;
            $code_data['member_miniapp_id'] = $this->member_miniapp_id;
            $code_data['member_id']         = $this->user->id;
            $code_data['auditid']           = $rel['auditid'];
            SystemMemberMiniappCode::edit(['member_miniapp_id' => $this->member_miniapp_id,'member_id' => $this->user->id],$code_data);
            return json(['code'=>200,'msg'=>'成功提交审核','url'=>url('system/passport.setting/index')]);
        }else{
            return json(['code'=>0,'msg'=>'读取体验二维码失败']); 
        }
    }

    /**
     * 强制撤销审核
     * @access public
     */
    public function restPass(){
        if(request()->isAjax()){
            $miniapp = WechatProgram::isTypes($this->member_miniapp_id);
            if(!$miniapp){
                return json(['code'=>0,'msg'=>'小程序还未授权,禁止操作']); 
            }
            $rel = $this->program->code->withdrawAudit();
            if($rel['errcode'] == 0){
                $data['is_commit']         = 2;
                $data['state']             = 0;
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $data['member_id']         = $this->user->id;
                SystemMemberMiniappCode::edit(['member_miniapp_id'=>$this->member_miniapp_id,'member_id' => $this->user->id],$data);
                return json(['code'=>200,'msg'=>'撤回成功']);
            }else{
                return json(['code'=>0,'msg'=>'撤回失败:'.$rel['errmsg']]);
            }
        }else{
            return json(['code'=>0,'msg'=>'读取体验二维码失败']); 
        }
    }

    /**
     * 发布小程序
     */
    public function sendApp(){
        if(request()->isAjax()){
            $rel = WechatProgram::isTypes($this->member_miniapp_id)->code->release();
            switch ($rel['errcode']) {
                case 0:
                    //修改发布状态
                    SystemMemberMiniappCode::edit(['member_miniapp_id' => $this->member_miniapp_id,'member_id' =>$this->user->id],['is_commit' => 4,'state' => 0,'member_miniapp_id'=>$this->member_miniapp_id,'member_id'=>$this->user->id]);
                    //同步本地版本
                    SystemMemberMiniappOrder::where(['id' => $this->member_miniapp->miniapp_order_id])->update(['update_var' => $this->member_miniapp->miniapp->template_id]);
                    return json(['code'=>200,'msg'=>'发布成功']);
                    break;
                case 85019:
                    return json(['code'=>0,'msg'=>'没有审核版本']);
                    break;
                case 85020:
                    return json(['code'=>0,'msg'=>'审核状态未满足发布']);
                    break;
                case -1:
                    return json(['code'=>0,'msg'=>'系统繁忙']);
                    break;
                default:
                    return json(['code'=>0,'msg'=>$rel['errmsg']]);
                    break;
            }
        }else{
            return json(['code'=>0,'msg'=>'小程序发布失败']); 
        }
    }
}
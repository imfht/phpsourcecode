<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\common\model\SystemUser;
use app\common\model\SystemUserLevel;
use app\popupshop\model\Agent;
use think\facade\Request;
use think\facade\Validate;

class User extends Manage
{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'代理管理','url'=>'javascript:;']]);
    }

   /**
     * 代理管理
     */
    public function agent(){ 
        $view['list'] = Agent::where(['member_miniapp_id' => $this->member_miniapp_id])->order('id desc')->paginate(20);;
        return view()->assign($view);   
    }


   /**
     * 会员列表
     */
    public function select(){ 
        if(request()->isAjax()){
            $ids = Request::param('ids/s');
            if(empty($ids)){
                return json(['code'=>0,'msg'=>'请选择要添加代理的用户']);
            }
            $result = Agent::add($this->member_miniapp_id,(array)ids($ids,true));
            if($result){
                return json(['code'=>302,'msg'=>'代理用户添加成功','data' =>[]]);
            }else{
                return json(['code'=>0,'msg'=>'代理用户添加操作失败']);
            }
        }else{
            $keyword = Request::param('keyword');
            $condition = [];
            if(!empty($keyword)){
                $condition['phone_uid'] = $keyword;
            }
            $condition['is_lock'] = 0;
            $condition['member_miniapp_id'] = $this->member_miniapp_id;
            $view['list']      = Agent::selects($condition);
            $view['keyword']   = $keyword;
            return view()->assign($view);   
        }
    }

    /**
     * 编辑用户
     * @access public
     */
    public function agentedit(){
        if(request()->isAjax()){
            $data = [
                'id'     => Request::param('id/d'),
                'rebate' => Request::param('rebate/d'),
            ];
            $result  = Agent::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $data['id']])->update(['rebate' =>$data['rebate']]);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url' => url('user/agent')]);
            }
        }else{
            $id = Request::param('id/d');
            $view['agent'] = Agent::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            if(!$view['agent']){
                return $this->error("404 NOT FOUND");
            } 
            $view['info']        = SystemUser::where(['member_miniapp_id' => $this->member_miniapp_id,'id' =>  $view['agent']['user_id']])->find();
            $view['user_number'] = SystemUserLevel::where(['parent_id' => $view['agent']['user_id']])->count();
            return view()->assign($view);
        }
    }

    //删除
    public function agentdelete(){
        $result = Agent::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => Request::param('id/d',0)])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功']);
        }else{
            return json(['code'=>403,'msg'=>'删除失败']);
        } 
    }
}
<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户公共调用模块
 */
namespace app\green\controller;
use app\common\controller\Manage;
use app\green\model\GreenMember;
use app\green\model\GreenOperate;

class Common extends Manage{

    public $mini_program      = [];
    public $where_operate     = [];
    public $operate_id        = 0;      //运营商ID
    public $operate           = [];     //运营商数据
    public $operate_name      = '全部'; //运营商数据
    public $founder           = true;   //是否创始人

    /**
     * 初始化当前应用管理员是不是运营商账户
     * @return void
     */
    public function initialize() {
        parent::initialize();
        if($this->user->parent_id){
            $operate = GreenMember::getOperate($this->user->id);
            if(empty($operate)){
                $this->error('未绑定运营商,请联系您的专属客服');
            }
            if($operate->operate->is_lock == 1){
                $this->error('运营商已被锁定,禁止登录');
            }
            $this->operate_id    = $operate->operate_id;             //运营商ID
            $this->operate       = $operate->operate;                //运营商信息
            $this->operate_name  = $operate->operate->operate_name;  //默认运营商名称
            $this->where_operate = ['operate_id', '=', $this->operate_id];
            $this->founder       = false;
        }else{
            $this->operate = GreenOperate::where(['member_miniapp_id' => $this->member_miniapp_id,'is_lock' => 0])->order('id desc')->select();
            $operate_id    = $this->request->param('operate_id/d',0);
            if($operate_id){
                $this->where_operate = ['operate_id','=',$operate_id];
            }
        }
        $this->mini_program     = ['member_miniapp_id' => $this->member_miniapp_id];
        $assign['operate_id']   = $this->operate_id;
        $assign['operate']      = $this->operate;
        $assign['founder']      = $this->founder;
        $assign['operate_name'] = $this->operate_name;
        $this->assign($assign);
    }
}
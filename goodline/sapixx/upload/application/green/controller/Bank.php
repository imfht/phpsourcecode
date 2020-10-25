<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 客户收益管理
 */
namespace app\green\controller;
use app\common\model\SystemUserBank;
use app\common\model\SystemUser;
use app\green\model\GreenBankCash;
use app\green\model\GreenConfig;
use app\green\model\GreenUser;

class Bank extends Common{


    public function initialize(){
        parent::initialize();
        if(!$this->founder){
            $this->error('您无权限操作');
        }
        $this->assign('pathMaps', [['name'=>'财务管理','url'=>url("bank/cash")]]);
    }

    /**
     * 客户提现
     */
    public function cash($types = 0){ 
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        $condition = [];
        $condition['GreenBankCash.member_miniapp_id'] = $this->member_miniapp_id;
        switch ($types) {
            case 1:
                $state = -1;
                break;
            case 2:
                $state = 1;
                break;
            default:
                $state = 0;
                break;
        }
        $condition['state'] = $state;
        $view['list']       = GreenBankCash::hasWhere('user', function($query) use ($keyword){
            $query->where('phone_uid', 'like', '%'.$keyword.'%');
        })->where($condition)->order('id desc')->paginate(20,false,['query' => ['types' => $types]]);
        $view['now_money']  = GreenBankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => 0])->sum('money');
        $view['realmoney']  = GreenBankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => 1])->sum('realmoney');
        $view['back_money'] = GreenBankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => -1])->sum('money');
        $view['keyword']    = $keyword;
        $view['types']      = $types;
        $view['pathMaps']   = [['name'=>' 提现管理','url' => url("bank/cash")]];
        return view()->assign($view);
    }

    /**
     * 客户审核
     */
    public function cashpass(int $id){
        if(request()->isAjax()){
            $data = [
                'id'         => input('post.id/d'),
                'ispass'     => input('post.ispass/d'),
                'miniapp_id' => $this->member_miniapp_id,
                'realmoney'  => input('post.realmoney/f')
            ];
            $validate = $this->validate($data,'Bank.cash');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = GreenBankCash::isPass($data,$this->member_miniapp_id);
            return json($result);
        }else{
            $cash = GreenBankCash::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();;
            if(empty($cash)){
                $this->error('未找到到申请提现内容');
            }
            $setting = GreenConfig::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            $view['cash']      = $cash;
            $view['realmoney'] = $cash->money;
            $view['bank']      = GreenUser::where(['uid' => $cash->user_id])->find();
            $view['info']      = SystemUserBank::where(['user_id' => $cash->user_id])->find();
            $view['wechat']    = User::where(['id' => $cash->user_id])->field('id,miniapp_uid,phone_uid,official_uid')->find();
            $view['config']    = $setting ;
            return view()->assign($view);
        }
    }
}
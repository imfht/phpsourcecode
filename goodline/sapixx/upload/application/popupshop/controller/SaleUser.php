<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 市场销售产品
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Sale;
use app\popupshop\model\SaleUser as AppSaleUser;
use think\facade\Request;
use think\helper\Time;

class SaleUser extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'客户商品','url'=>url("popupshop/saleUser/index")]]);
    }


    /**
     * 列表
     */
    public function index(){
        $time      = Request::param('time/d', 0);
        $uid       = Request::param('uid/d');
        $starttime = Request::param('starttime/s');
        $endtime   = Request::param('endtime/s');
        $condition[]        = ['member_miniapp_id', '=', $this->member_miniapp_id];
        if($time){
            switch ($time) {
                case 2:
                    list($start, $end) = Time::yesterday();
                    break;
                case 30:
                    list($start, $end) = Time::month();
                    break;
                case 60:
                    list($start, $end) = Time::lastMonth();
                    break;
                default:
                    list($start, $end) = Time::today();
                    break;
            }
            $condition[] = ['update_time','>=',$start];
            $condition[] = ['update_time','<=',$end];
        }else{
            if($starttime){
                $condition[] = ['update_time','>=',strtotime($starttime)];
            }
            if($endtime){
                $condition[] = ['update_time','<=',strtotime($endtime)];
            }
        }
        if($uid){
            $condition[] = ['user_id','=',$uid];
        }
        $view['status']    = Request::param('status', 0);
        $view['lists']     = AppSaleUser::where($condition)->order('id desc')->paginate(20, false, ['query' => ['status' => $view['status']]]);
        $view['count']     = AppSaleUser::where($condition)->count();
        $view['money']     = AppSaleUser::where($condition)->sum('rebate');
        $view['time']      = $time;
        $view['uid']       = $uid;
        $view['starttime'] = $starttime;
        $view['endtime']   = $endtime;
        return view()->assign($view);
    }

     /**
     * 删除
     */
    public function delete(){
        $id = $this->request->param('id');
        if(empty($id)){
            return enjson(0);
        }
        Sale::where(['member_miniapp_id' => $this->member_miniapp_id,'sales_user_id' => $id])->delete();  //删除已上架套装
        AppSaleUser::destroy($id);
        return enjson(200);
    }
}
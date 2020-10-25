<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 */
namespace app\guard\controller\home;
use app\common\controller\Official;
use app\guard\model\Guard;
use app\guard\model\GuardHistory;
use app\guard\model\GuardUser;

class Manage extends Official {

    public function initialize() {
        parent::initialize();
        $this->view->engine->layout(false);
    }

    public function index(){
        $view['guard'] = Guard::where(['member_miniapp_id' => $this->member_miniapp_id, 'id' => $this->request->param('id/d')])->find();
        return view()->assign($view);
    }
    public function list($types = 1){
        $condition = [];
        $where     = [];
        $name      = $this->request->param('name/s');
        $phone     = $this->request->param('phone/s');
        $idcard    = $this->request->param('idcard/s');
        $start     = $this->request->param('start/s');
        $end       = $this->request->param('end/s');
        if($start){
            $condition[] = ['update_time','>=',strtotime($start)];
        }
        if($end){
            $condition[] = ['update_time','<=',strtotime($end)];
        }
        if($name){
            $where[] = ['name','=',$name];
        }
        if($phone){
            $where[] = ['phone','=',$phone];
        }
        if($idcard){
            $where[] = ['idcard','=',$idcard];
        }
        if($types){
            $info = GuardHistory::where(['member_miniapp_id' => $this->member_miniapp_id])->where($condition)
                ->whereIn('uid',GuardUser::where($where)->column('uid'))->paginate(10, false)->order('id');
            foreach ($info as $key => $value) {
                $info[$key]['account']     = $value->account;
                $info[$key]['update_time'] = date('Y-m-d H:i:s',$value->update_time);
            }
            return enjson(200,'成功',$info);
        }else{
            header("Content-type: text/plain");
            header("Accept-Ranges: bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=cash_".date('Y-m-d').".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $info = GuardHistory::where(['member_miniapp_id' => $this->member_miniapp_id])->where($condition)
                ->whereIn('uid',GuardUser::where($where)->column('uid'))->order('id')->select();
            foreach ($info as $key => $value) {
                $info[$key]['account']     = $value->account;
                $info[$key]['update_time'] = date('Y-m-d H:i:s',$value->update_time);
            }
            $view['list'] = $info;
            return view('excel',$view);
        }
    }
}
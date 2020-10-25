<?php
namespace app\member\controller;

use app\common\controller\MemberBase;
use app\common\model\Module_buyer AS BuyerModel;

class Market extends MemberBase
{
    /**
     * 列出可购买的应用
     * @return mixed|string
     */
    public function index(){
        $listdb = [];
        foreach (modules_config() AS $rs){
            if (!$rs['is_sell']) {
                continue;
            }
            $listdb[] = $this->format_data($rs,1);
        }
        foreach (plugins_config() AS $rs){
            if (!$rs['is_sell']) {
                continue;
            }
            $listdb[] = $this->format_data($rs,0);
        }
        $this->assign('listdb',$listdb);
        return $this->fetch();
    }
    
    protected function format_data($rs=[],$is_m=0){
        $rs['is_m'] = $is_m;
        $rs['_money'] = $this->format_money($rs['money']);
        if ($rs['admingroup']!='' && in_array($this->user['groupid'], explode(',', $rs['admingroup']))) {
            $rs['is_power'] = true;
        }
        $vs = BuyerModel::where('uid',$this->user['uid'])->where('mid',$is_m?$rs['id']:-$rs['id'])->find();
        if ($vs) {
            $rs['endtime'] = $vs['endtime'];
        }else{
            $rs['endtime'] = -1;
        }
        return $rs;
    }
    
    /**
     * 把字符串转为数组
     * @param string $content
     * @return unknown[][]|array[][]
     */
    protected function format_money($content=''){
        $array = [];
        $detail = explode("\r\n", trim($content,"\r\n"));
        foreach($detail AS $value){
            if (!$value) {
                continue;
            }
            list($day,$money,$title) = explode("|", $value);
            $array[] = [
                'day'=>$day,
                'money'=>$money,
                'title'=>$title,
            ];
        }
        return $array;
    }
    
    /**
     * 免费体验应用
     * @param number $id 应用ID
     * @param number $is_m 频道还是插件
     */
    public function test($id=0,$is_m=0){
        $info = $is_m ? modules_config($id) : plugins_config($id);
        
        if (!$info['is_sell']) {
            $this->error('系统并没有上架该应用');
        }elseif(!$info['testday']){
            $this->error('该应用并没有免费试用期');
        }
        
        $array = [
            'uid'=>$this->user['uid'],
            'mid'=>$is_m ? $id : -$id,
        ];
        
        if (BuyerModel::where($array)->find()) {
            $this->error('该应用你已经试用过了,你只能选择购买!');
        }
        
        $array['endtime'] = time()+$info['testday']*24*3600;
        
        if (BuyerModel::create($array)) {
            $this->success('恭喜你,你获得了 '.$info['testday'].' 天的试用期','index');
        }else{
            $this->error('数据库执行失败!');
        }
    }
    
    /**
     * 免费购买
     * @param number $id 频道或插件ID
     * @param number $is_m 是否为频道
     */
    public function freebuy($id=0,$is_m=0){
        $info = $is_m ? modules_config($id) : plugins_config($id);
        
        if (!$info['is_sell']) {
            $this->error('系统并没有上架该应用');
        }elseif( $info['admingroup']=='' || !in_array($this->user['groupid'], explode(',', $info['admingroup'])) ){
            $this->error('你没权限免费使用');
        }
        
        $array = [
            'uid'=>$this->user['uid'],
            'mid'=>$is_m ? $id : -$id,
        ];
        $endtime = 0;
        $rs = BuyerModel::where($array)->find();
        if ($rs) {
            if ($this->user['group_endtime']) {
                $endtime = $this->user['group_endtime'];
                if ($rs['endtime']>time()) {    //他原有的时效不要作废处理
                    //$endtime += $rs['endtime']-time();
                }
            }
            $result = BuyerModel::where('id',$rs['id'])->update(['endtime'=>$endtime]);
        }else{
            if ($this->user['group_endtime']) {
                $endtime = $this->user['group_endtime'];
                if ($info['testday']) { //体验试用时效也给他补上
                    //$endtime += $info['testday']*24*3600;
                }
            }
            $array['endtime'] = $endtime;
            $result = BuyerModel::create($array);
        }
        if ($result){
            $this->success('免费购买成功,'.($this->user['group_endtime']?'有效截止期是:'.date('Y-m-d H:i',$this->user['group_endtime']):'长久有效'),'index');
        }else{
            $this->error('数据库执行失败!');
        }
    }
    
    /**
     * 付费购买应用
     * @param number $id 应用ID
     * @param number $is_m 频道还是插件
     * @param number $type 购买哪种时长
     */
    public function buy($id=0,$is_m=0,$type=0){
        $info = $is_m ? modules_config($id) : plugins_config($id);
        if (!$info['is_sell']) {
            $this->error('系统并没有上架该应用');
        }
        
        $cfg = $this->format_money($info['money']);
        
        if ($cfg[$type]['money']>$this->user['rmb']) {
            $this->error('你的余额不足 '.$cfg[$type]['money'].' 元');
        }
        
        $array = [
            'uid'=>$this->user['uid'],
            'mid'=>$is_m ? $id : -$id,
        ];
        $endtime = time();
        $rs = BuyerModel::where($array)->find();
        if ($rs) {
            if ($rs['endtime']>time()) {
                $endtime = $rs['endtime'];
            }
            $endtime += $cfg[$type]['day']*24*3600;
            $result = BuyerModel::where('id',$rs['id'])->update(['endtime'=>$endtime]);
        }else{
            $endtime += ($cfg[$type]['day']+$info['testday'])*24*3600;
            $array['endtime'] = $endtime;
            $result = BuyerModel::create($array);
        }
        
        if ($result) {
            add_rmb($this->user['uid'],-$cfg[$type]['money'],0,'购买应用:'.$info['name']);
            $this->success('购买成功','index');
        }else{
            $this->error('数据库执行失败!');
        }
    }

}

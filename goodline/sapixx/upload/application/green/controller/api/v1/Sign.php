<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 招募
 */
namespace app\green\controller\api\v1;
use app\common\controller\Api;
use app\green\model\GreenSign;
use app\green\model\GreenSignConfig;
use app\green\model\GreenUser;
use think\helper\Time;


class Sign extends Api {
    /**
     * 签到数据
     */
    public function index(){
        $param['sign'] = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        list($start, $end) = Time::today();
        $info   = GreenSign::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->where('signtime','>',$start)->where('signtime','<',$end)->find();
        if(empty($info)){
            list($start, $end) = Time::yesterday();
            $info   = GreenSign::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->where('signtime','>',$start)->where('signtime','<',$end)->find();
            if(empty($info)){
                return enjson(204,'empty');
            }else{
                return enjson(202,'success', $info);
            }
        }
        return enjson(200,'success', $info);
    }

    /**
     * @return \think\response\Json
     * 签到
     */
    public function add(){
        $this->isUserAuth();
        $param['sign'] = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson($rel['code'],'签名验证失败');
        }
        list($start, $end) = Time::today();
        $today   = GreenSign::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->where('signtime','>',$start)->where('signtime','<',$end)->find();
        if($today){
            return enjson(0,'已签到');
        }
        list($start, $end) = Time::yesterday();
        $info   = GreenSign::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->where('signtime','>',$start)->where('signtime','<',$end)->find();
        $greenUser = GreenUser::where(['member_miniapp_id' =>  $this->miniapp_id,'uid' => $this->user->id])->find();
        if($info){
            $days =  $info->days+1;
            $config = GreenSignConfig::where(['member_miniapp_id' => $this->miniapp_id,'config_id' => $days])->find();
            if($config){
                $sign                    = new GreenSign;
                $sign->member_miniapp_id = $this->miniapp_id;
                $sign->uid               = $this->user->id;
                $sign->startime          = $info->startime;
                $sign->signtime          = time();
                $sign->days              = $days;
                $sign->points            = $config->point;
                $sign->save();

                if($greenUser){
                    GreenUser::where(['id' => $greenUser->id])->update(['points' => $greenUser->points + $config->point,'update_time' => time()]);
                }else{
                    GreenUser::create(['member_miniapp_id' =>$this->miniapp_id,'uid' => $this->user->id,'points' =>$config->point,'weight' => 0,'create_time' => time(),'update_time' => time()]);
                }
            }else{
                $config = GreenSignConfig::where(['member_miniapp_id' => $this->miniapp_id,'config_id' => 1])->find();
                if($config){
                    $info                    = new GreenSign;
                    $info->member_miniapp_id = $this->miniapp_id;
                    $info->uid               = $this->user->id;
                    $info->startime          = time();
                    $info->signtime          = time();
                    $info->days              = 1;
                    $info->points            = $config->point;
                    $info->save();
                    if($greenUser){
                        GreenUser::where(['id' => $greenUser->id])->update(['points' => $greenUser->points + $config->point,'update_time' => time()]);
                    }else{
                        GreenUser::create(['member_miniapp_id' =>$this->miniapp_id,'uid' => $this->user->id,'weight' => 0,'points' =>$config->point,'create_time' => time(),'update_time' => time()]);
                    }
                }
            }
        }else{
            $config = GreenSignConfig::where(['member_miniapp_id' => $this->miniapp_id,'config_id' => 1])->find();
            if($config){
                $info                    = new GreenSign;
                $info->member_miniapp_id = $this->miniapp_id;
                $info->uid               = $this->user->id;
                $info->startime          = time();
                $info->signtime          = time();
                $info->days              = 1;
                $info->points            = $config->point;
                $info->save();
                if($greenUser){
                    GreenUser::where(['id' => $greenUser->id])->update(['points' => $greenUser->points + $config->point,'update_time' => time()]);
                }else{
                    GreenUser::create(['member_miniapp_id' =>$this->miniapp_id,'uid' => $this->user->id,'weight' => 0,'points' =>$config->point,'create_time' => time(),'update_time' => time()]);
                }
            }
        }
        if($info){
            return enjson(200,'成功',$info);
        }else{
            return enjson(0,'失败');
        }

    }
}
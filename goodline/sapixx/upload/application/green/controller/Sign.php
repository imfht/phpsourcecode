<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 文章管理
 */
namespace app\green\controller;
use app\green\model\GreenSign;
use app\green\model\GreenSignConfig;
use think\facade\Request;
use think\helper\Time;

class Sign extends Common{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'签到管理','url'=>url("sign/index")]]);
    }

    /**
     * 列表
     */
    public function index()
    {
        $condition = [];
        $time      = Request::param('time/d', 0);
        $starttime = Request::param('starttime/s');
        $endtime   = Request::param('endtime/s');
        if ($time) {
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
            $condition[] = ['signtime', '>=', $start];
            $condition[] = ['signtime', '<=', $end];
        } else {
            if ($starttime) {
                $condition[] = ['signtime', '>=', strtotime($starttime)];
            }
            if ($endtime) {
                $condition[] = ['signtime', '<=', strtotime($endtime)];
            }
        }
        $uid = Request::param('uid/d');
        if (!empty($uid)) {
            $condition[] = ['uid', '=', $uid];
        }
        $view['lists']     = GreenSign::where($this->mini_program)->where($condition)->order('id desc')->paginate(20, false, ['query' => ['starttime' => $starttime, 'endtime' => $endtime, 'time' => $time]]);
        $view['uid']       = $uid;
        $view['time']      = $time;
        $view['starttime'] = $starttime;
        $view['endtime']   = $endtime;
        return view()->assign($view);
    }
    /**
     * 列表
     */
    public function config(){
        $view['lists'] = GreenSignConfig::where($this->mini_program)->order('id')->select();
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'config_id' => input('post.config_id/d'),
                'point'     => input('post.point/d'),
            ];
            $validate = $this->validate($data,'sign.save');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenSignConfig::create(['member_miniapp_id' => $this->member_miniapp_id,'config_id' => $data['config_id'],'point' => $data['point']]);
            if($result){
                return enjson(200,'操作成功',['url' => url('sign/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['info'] =GreenSignConfig::where($this->mini_program)->order('config_id desc,id desc')->find();
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'        => input('post.id/d'),
                'config_id' => input('post.config_id/d'),
                'point'     => input('post.point/d'),
            ];
            $validate = $this->validate($data,'sign.save');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenSignConfig::where(['id' => $data['id']])->update(['member_miniapp_id' => $this->member_miniapp_id,'point' => $data['point']]);
            if($result){
                return enjson(200,'操作成功',['url' => url('news/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id  = input('get.id/d');
            $view['info'] = GreenSignConfig::where(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(int $id){
        $info = GreenSignConfig::where(['id' => $id])->where($this->mini_program)->find();
        if($info){
            $next = GreenSignConfig::where('config_id','>',$info->config_id)->where($this->mini_program)->find();
            if($next){
                return enjson(403,'操作失败,请先删除后面的配置');
            }else{
                $result = $info->delete();
                if($result){
                    return enjson(200,'操作成功');
                }else{
                    return enjson(403,'操作失败');
                }
            }
        }
        return enjson(403,'操作失败');
    }
}
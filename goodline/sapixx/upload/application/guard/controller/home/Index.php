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

class Index extends Official{

    public function initialize() {
        parent::initialize();
        $this->view->engine->layout(false);
    }

    /**
     * 签到
     */
    public function index(){
        if(request()->isAjax()){
            $data = [
                'id'                => $this->request->param('id/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
                'car_num'           => $this->request->param('car_num/s'),
                'name'              => $this->request->param('name/s'),
                'idcard'            => $this->request->param('idcard/d'),
                'pass_out'          => $this->request->param('pass_out/s','进'),
                'phone'             => $this->request->param('phone/s'),
                'temperature'       => $this->request->param('temperature/s'),
                'why'               => $this->request->param('why/s'),
                'is_danger'         => $this->request->param('is_danger/s','off')
            ];
            $validate = $this->validate($data,'Send.sign');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $info = GuardUser::where(['uid' => $this->user->id])->find();
            if(empty($info)){
                $guard = [
                    'member_miniapp_id' => $data['member_miniapp_id'],
                    'uid'               => $this->user->id,
                    'name'              => $data['name'],
                    'idcard'            => $data['idcard'],
                    'phone'             => $data['phone'],
                    'pass_out'          => $data['pass_out'] == '进' ? '进': '出',
                    'car_num'           => $data['car_num'],
                    'create_time'       => time(),
                    'update_time'       => time()
                ];
                $info = GuardUser::create($guard);
            }else{
                $info->update_time = time();
                $info->pass_out    = $data['pass_out'] == '进' ? '进': '出';
                $info->car_num     = $data['car_num'];
                $info->save();
            }
            $history['member_miniapp_id'] = $data['member_miniapp_id'];
            $history['gid']               = $data['id'];
            $history['uid']               = $info->uid;
            $history['car_num']           = $data['car_num'];
            $history['is_danger']         = $data['is_danger'] == 'off' ? 0: 1;
            $history['temperature']       = $data['temperature'];
            $history['pass_out']          = $data['pass_out'] == '进' ? '进' : '出';
            $history['why']               = $data['why'];
            $history['update_time']       = time();
            $rel = GuardHistory::create($history);
            if(empty($rel)){
                return enjson(0,'填写失败,请重新创建一下');
            }
            return enjson(200,'成功,允许通行',$rel);
        }else{
            $view['guard'] = Guard::where(['member_miniapp_id' => $this->member_miniapp_id, 'id' => $this->request->param('id/d')])->find();
            $view['info']  = GuardUser::where(['member_miniapp_id' => $this->member_miniapp_id, 'uid' => $this->user->id])->find();
            if(!$view['guard']){
                $this->error('请先增加主体');
            }
            return view()->assign($view);
        }
    }

    /**
     * 签到
     */
    public function history(){
        $info = GuardHistory::where(['member_miniapp_id' => $this->member_miniapp_id,'uid' => $this->user->id])->limit(10)->order('id desc')->select();
        foreach ($info as $key => $value) {
            $info[$key]['account']     = $value->account;
            $info[$key]['update_time'] = date('Y-m-d H:i:s',$value->update_time);
        }
        return enjson(200,'成功',$info);
    }
}
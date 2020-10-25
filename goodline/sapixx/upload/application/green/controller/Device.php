<?php
namespace app\green\controller;
use app\common\model\SystemUser;
use app\green\model\GreenAlarm;
use app\green\model\GreenDevice;
use think\facade\Request;
use think\facade\Validate;
use think\helper\Time;

class Device extends Common{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'设备管理','url'=>url("green/device/index")]]);
    }

    /**
     * 列表
     */
    public function index(int $types = 0,int $operate_id = 0){
        $condition   = [];
        if($this->founder){
            if($operate_id > 0){
                $condition[] = ['operate_id','=',$operate_id];
            }
        }else{
            $condition[] = ['operate_id','=',$this->operate_id];
        }
        $time      = Request::param('time/d',0);
        $starttime = Request::param('starttime/s');
        $endtime   = Request::param('endtime/s');
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
            $condition[] = ['create_time','>=',$start];
            $condition[] = ['create_time','<=',$end];
        }else{
            if($starttime){
                $condition[] = ['create_time','>=',strtotime($starttime)];
            }
            if($endtime){
                $condition[] = ['create_time','<=',strtotime($endtime)];
            }
        }
        $keyword = Request::param('keyword/s');
        if(!empty($keyword)){
            $condition[] = ['title|device_id','like','%'.$keyword.'%'];
        }
        $view['alarm_num']    = GreenAlarm::where($this->mini_program)->where($condition)->where(['state' => 0])->count();
        $view['line_num']     = GreenDevice::where($this->mini_program)->where($condition)->where(['state' => 0])->count();
        $view['off_line_num'] = GreenDevice::where($this->mini_program)->where($condition)->where(['state' => 1])->count();
        $view['lists']        = GreenDevice::where($this->mini_program)->where($condition)->where(['state' => $types ? 1 : 0])->order('id desc')->paginate(20, false, ['query' => ['types' => $types, 'starttime' => $starttime, 'endtime' => $endtime, 'time' => $time, 'operate_id' => $operate_id]]);
        $view['keyword']      = $keyword;
        $view['types']        = $types;
        $view['operate_id']   = $operate_id;
        $view['time']         = $time;
        $view['starttime']    = $starttime;
        $view['endtime']      = $endtime;
        return view()->assign($view);
    }


    /**
     * 告警列表
     */
    public function alarm(int $id = 0){
        $view['lists']        = GreenAlarm::where($this->mini_program)->where(['device_id' => $id])->order('create_time desc')->paginate(20, false, ['query' => ['id' => $id]]);
        return view()->assign($view);
    }

    /**
     * 设备地图
     */
    public function deviceMap(int $id = 0,int $types = 0,int $operate_id = 0){
        $condition   = [];
        if($this->founder){
            if($operate_id > 0){
                $condition[] = ['operate_id','=',$operate_id];
            }
        }else{
            $condition[] = ['operate_id','=',$this->operate_id];
        }
        $keyword = Request::param('keyword/s');
        if(!empty($keyword)){
            $condition[] = ['title|device_id','like','%'.$keyword.'%'];
        }
        if(empty($id)){
            $list  = GreenDevice::where($this->mini_program)->where($condition)->where(['state' => $types ? 1 : 0])->field("id,longitude,latitude,title,device_id")->select();
        }else{
            $list  = GreenDevice::where($this->mini_program)->where(['id' => $id])->field("id,longitude,latitude,title,device_id")->find();
            
            $list->danger;
        }
        foreach ($list as $key => $value){
            if(!empty($value->danger)){
                $list[$key]->danger = 1;
            }
        }
        $view['list']       = json_encode($list);
        $view['id']         = $id;
        $view['types']      = $types;
        $view['keyword']    = $keyword;
        $view['operate_id'] = $operate_id;
        return view()->assign($view);
    }
    /**
     * 选择所属用户
     */
    public function selectManage(){
        $keyword = Request::param('keyword');
        $input   = Request::param('input');
        $condition = [];
        if(!empty($keyword)){
            if(Validate::isMobile($keyword)){
                $condition[] = ['phone_uid','=',$keyword];
            }else{
                $condition[] = ['nickname','like','%'.$keyword.'%'];
            }
        }
        $view['list']    = SystemUser::where($this->mini_program)->where($condition)->order('id desc')->paginate(10,false,['query' => ['input' => $input,'keyword' => $keyword]]);
        $view['keyword'] = $keyword;
        $view['input']   = $input;
        $view['id']      = $this->member_miniapp_id;
        
        return view()->assign($view);
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => Request::param('id/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
                'device_id'         => Request::param('device_id/s'),
                'manage_uid'        => Request::param('manage_uid/d', 0),
                'operate_id'        => $this->founder ? Request::param('operate_id/d') : $this->operate_id,
                'title'             => Request::param('title/s'),
                'address'           => Request::param('address/s'),
                'longitude'         => Request::param('longitude/s'),
                'latitude'          => Request::param('latitude/s'),
            ];
            $validate = $this->validate($data,'GreenDevice.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $device = GreenDevice::where(['device_id' => $data['device_id']])->find();
            $result = 0;
            if(empty($data['id']) && empty($device)){
                $data['create_time'] = time();
                $data['update_time'] = time();
                $result = GreenDevice::create($data);
            }else if($device->id == $data['id']){
                $result = GreenDevice::where(['id' => $data['id']])->update($data);
            }
            if($result){
                return enjson(200,'操作成功',['url'=>url('device/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['info'] = GreenDevice::where($this->mini_program)->where(['id' => $this->request->param('id/d')])->find();
            return view()->assign($view);
        }
    }
    //删除
    public function delete(int $id){
        $result = GreenDevice::destroy($id);
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(0,'操作失败');
        }
    }
    /**
     *  根据地址位置转换
     * @return void
     */
    public function baidu($address){
        $view['address']  = $address;
        
        return view()->assign($view);
    }
}
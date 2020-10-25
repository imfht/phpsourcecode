<?php
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\common\model\SystemUserAddress;
use app\common\facade\Inform;
use app\green\model\GreenDevice;
use app\green\model\GreenRetrieve;
use app\green\model\GreenStaff;
use app\green\model\GreenUser;
use app\green\model\GreenUserLog;
use think\helper\Time;


class Index extends Base{

 
    public function user(){
        $this->isUserAuth();
        $param['signkey'] = $this->request->param('signkey/s');
        $param['sign']    = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if ($rel['code'] != 200) {
            return enjson(500, '签名验证失败');
        }
        $info = GreenUser::where(['member_miniapp_id' => $this->miniapp_id])->where(['uid' => $this->user->id])->find();
        if($info){
            //用户管理的回收柜
            $info->list = GreenDevice::where(['member_miniapp_id' => $this->miniapp_id,'manage_uid' => $this->user->id])->column('device_id');;
            //加入天数
            $info->day = (int)((time() - $this->user->create_time)/(24*60*60));
            //投递地点
            $info->address = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->group('device_id')->count();
            //累计投递次数
            $info->count = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->count();
            //累计投递重量
            $info->weight = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id,'uid' => $this->user->id])->sum('weight');
            //共有多少人参与
            $info->join = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id])->group('uid')->count();
            //排名
            $info->top = GreenUser::where(['member_miniapp_id' => $this->miniapp_id])->where('weight','>',$info->weight)->count() + 1;
            $info->weight = round($info->weight/1000);
            return enjson(200,'success', $info);
        }else{
            $data = [];
            $data['points']  = 0;
            $data['list']    = 0;
            $data['day']     = 0;
            $data['address'] = 0;
            $data['count']   = 0;
            $data['weight']  = 0;
            $data['join']    = 0;
            $data['top']     = 0;
            $data['weight']  = 0;
            return enjson(200,'success', $data);
        }
    }

    /**
     * 地图
     */
    public function map(){
        $param['longitude'] = $this->request->param('longitude/s','');
        $param['latitude']  = $this->request->param('latitude/s','');
        $param['signkey']   = $this->request->param('signkey/s');
        $param['sign']      = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        $data = [];
        $info = GreenDevice::where(['member_miniapp_id' => $this->miniapp_id])->where(['state' => 0])->field("id,longitude,latitude,title,address")->select()->toArray();
        $data['device'] = $info;
        if(empty($data['device'])){
            return enjson(204,'empty');
        }
        if(empty($param['longitude']) && empty($param['latitude'])){
            $data['near'] = [];
        }else{
            $mps = txMap_to_bdMap($param['latitude'],$param['longitude']);
            $param['latitude']  = $mps['lat'];
            $param['longitude'] = $mps['lng'];
            $data['near'] = model('GreenDevice')->selectNear($param);
        }
        return enjson(200,'success',$data);
    }

    /**
     * 记录
     */
    public function log(){
        $this->isUserAuth();
        $param['today'] = $this->request->param('today/d',0);
        $param['page']  = $this->request->param('page/d',1);
        $param['sign']  = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        $condition[] = ['uid','=',$this->user->id];
        list($start,$end) = Time::month();
        if($param['today']){
            $condition[] = ['create_time','<=',$start];
        }else{
            $condition[] = ['create_time','>=',$start];
            $condition[] = ['create_time','<=',$end];
        }
        $count  = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->count();
        $weight = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->sum('weight');
        $info   = GreenUserLog::withAttr('Create_time',function($value,$data) {
            return date('Y-m-d H:i',$value);
        })->where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->order('id desc')->paginate(10)->toArray();
        if(empty($info['data'])){
            return enjson(204,'empty');
        }else{
            return enjson(200,'success',['list' => $info['data'],'count' => $count,'weight' => $weight]);
        }
    }

    /**
     * 当月账单
     */ 
    public function moonBill(){
        $this->isUserAuth();
        $param['signkey']  = $this->request->param('signkey/s');
        $param['sign']    = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        $condition[] = ['uid','=',$this->user->id];
        list($start,$end) = Time::month();
        $condition[] = ['create_time','>=',$start];
        $condition[] = ['create_time','<=',$end];
        $count  = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->count();
        $weight = GreenUserLog::where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->sum('weight');
        return enjson(200,'success',['count' => $count,'weight' => round($weight/1000)]);
    }


    /**
     * @return \think\response\Json
     * 添加预约信息
     */
    public function add(){
        $this->isUserAuth();
        if(request()->isPost()){
            $param = [
                'weight'    => $this->request->param('weight/d'),
                'date'      => $this->request->param('date/s'),
                'address'   => $this->request->param('address/s', 0),
                'message'   => $this->request->param('message/s'),
                'longitude' => $this->request->param('longitude/s'),
                'latitude'  => $this->request->param('latitude/s'),
                'sign'      => $this->request->param('sign/s'),
            ];
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson($rel['code'],'签名验证失败');
            }
            $validate = $this->validate($param,'Order.add');
            if(true !== $validate){
                return enjson(403,$validate);
            }
            //读取收货地址
            $address = SystemUserAddress::where(['user_id'=>$this->user->id,'address' =>$param['address']])->find();
            if(empty($address)){
                return enjson(403,'请选择地址');
            }
            //查询运营商
            $info = model('GreenOperate')->selectAll($param);
            if( $info){
                $order['member_miniapp_id'] = $this->miniapp_id;
                $order['uid']               = $this->user->id;
                $order['realname']          = $address['name'];
                $order['phone']             = $address['telphone'];
                $order['address']           = $address['address'];
                $order['order_no']          = $this->user->invite_code . order_no();
                $order['message']           = $param['message'];
                $order['longitude']         = $param['longitude'];
                $order['latitude']          = $param['latitude'];
                $order['create_time']       = time();
                $order['date']              = $param['date'];
                $order['weight']            = $param['weight'];
                $result =  GreenRetrieve::create($order);
                if($result){
                    //通知到回收员
                    $list = GreenStaff::where(['member_miniapp_id' => $this->miniapp_id,'operate_id' => $info[0]['id']])->select()->toArray();
                    foreach ($list as $info){
                        Inform::sms($info['uid'],$this->miniapp_id,['title' =>'业务进展通知','type' => '新回收申请','content' =>'您有新的回收任务','state' => '待回收']);
                    }
                    return enjson(200,'操作成功',['url'=>url('category/index')]);
                }else{
                    return enjson(0);
                }
            }else{
                return enjson(403,'未查找到附近运营商');
            }
        }
    }

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 回收员设置回收状态
     */
    public function retrieveState(){
        $this->isUserAuth();
        if(request()->isPost()){
            $param = [
                'retrieve_id' => $this->request->param('retrieve_id/d'),
                'sign'        => $this->request->param('sign/s'),
            ];
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson($rel['code'],'签名验证失败');
            }
            $info = GreenRetrieve::where(['member_miniapp_id' => $this->miniapp_id,'id' => $param['retrieve_id']])->find();
            if($info){
                $info->state       = 1;
                $info->update_time = time();
                $info->save();
                return enjson(200,'success');
            }else{
                return enjson(0);
            }
        }
    }
}
<?php
namespace app\green\swoole;
use app\green\model\GreenAlarm;
use app\green\model\GreenDevice;
use app\green\model\GreenDeviceState;
use app\green\model\GreenUser;
use app\green\model\GreenUserLog;
use think\swoole\Server;
class Test extends Server
{
    protected $host = '0.0.0.0';
    protected $swoole;
    protected $port = 8080;
    protected $serverType = 'server';
    protected $mode = SWOOLE_PROCESS;
    protected $sockType = SWOOLE_SOCK_UDP;
    protected $option = [
        'worker_num'=> 8,
        'daemonize'	=> false,
        'backlog'	=> 128,
    ];
    public function onPacket($server, $data, $fd)
    {
        try{
            $buffer = str_split(bin2hex($data),2);
            var_dump($buffer);
            switch (count($buffer)) {
                //回收箱心跳包
                case 4:
                    $result = $this->checkHeart($buffer);
                    if($result){
                        $info   = GreenDevice::where(['device_id' => $result])->find();
                        if ($info) {
                            if($info->state == 1){
                                $info->state = 0;
                                $this->send($server, $fd['address'], $fd['port'], [0x53, 0xAB]);
                            }
                            $info->ip          = $fd['address'];
                            $info->port        = $fd['port'];
                            $info->update_time = time();
                            $info->save();
                        }
                    }else{
                        $this->send($server, $fd['address'], $fd['port'], [0x53, 0x00]);
                    }
                    break;
                //回收箱 状态变化
                case 12:
                    if($this->checkSum($buffer)){
                        //设备
                        $device   = GreenDevice::where(['device_id' => hexdec(implode("", [$buffer[2], $buffer[3]]))])->find();
                        //回收口开
                        if($buffer[5] == '4f'){
                            $info = GreenDeviceState::where(['device_id' => $device->device_id,'type' => 2,'state' => 0])->find();
                            if($info){
                                $this->send($server, $info->ip, $info->port, [0x53, 0x4F]);
                                GreenDeviceState::where(['id' => $info->id])->update(['state' => 1,'update_time' => time(),'device_content' => bin2hex($data)]);
                                break;
                            }
                        }
                        //箱满
                        if($buffer[6] == '31'){
                            GreenAlarm::create(['device_id' => $device->device_id,'operate_id' => $device->operate_id,'content' => '箱满','create_time' => time(),'member_miniapp_id' => $device->member_miniapp_id]);
                        }else{
                            $alarm = GreenAlarm::where(['device_id' => $device->device_id,'state' => 0,'member_miniapp_id' => $device->member_miniapp_id])->find();
                            if($alarm){
                                GreenAlarm::where(['id' => $alarm->id])->where(['state' => 1,'relieve_time' => time()]);
                                break;
                            }
                        }
                        //投口开
                        if($buffer[4] == '4f' && $device->type == 0){
                            $info = GreenDeviceState::where(['device_id' => $device->device_id,'type' => 0,'state' => 0])->find();
                            if($info){
                                $this->send($server, $info->ip, $info->port, [0x53, 0x4F]);
                                GreenDeviceState::where(['id' => $info->id])->update(['state' => 1,'update_time' => time(),'device_content' => bin2hex($data)]);
                                GreenDevice::where(['device_id' => $device->device_id, ])->update(['type' => 1]);
                            }
                        }else if($buffer[4] == '43' && $device->type == 1){ //投口关
                            $begin = GreenDeviceState::where(['device_id' => $device->device_id,'type' => 0,'state' => 1])->order('id desc,update_time desc')->limit('1')->select();
                            if(count($begin) > 0){
                                $info = GreenDeviceState::where(['device_id' => $device->device_id,'type' => 1,'state' => 0])->find();
                                if($info){
                                    GreenDeviceState::where(['id' => $info->id])->update(['state' => 1,'update_time' => time(),'device_content' => bin2hex($data)]);
                                    $this->send($server, $info->ip, $info->port, [0x53, 0x43]);
                                }else{
                                    GreenDeviceState::where(['id' => $begin[0]->id])->update(['update_time' => time(),'end_content' => bin2hex($data)]);
                                    $this->send($server, $begin[0]->ip, $begin[0]->port, [0x53, 0x43]);
                                }
                                //计算重量
                               $result = $this->weight(str_split($begin[0]->device_content,2),$buffer);
                               if($result > 0){
                                   //创建用户数据
                                   GreenUserLog::create(['member_miniapp_id' => $device->member_miniapp_id,'uid' => $begin[0]->uid,'device_id' => $device->device_id,'create_time' => time(),'weight' => $result]);
                                   $greenUser = GreenUser::where(['member_miniapp_id' => $device->member_miniapp_id,'uid' => $begin[0]->uid])->find();
                                   if($greenUser){
                                       GreenUser::where(['id' => $greenUser->id])->update(['weight' => $greenUser->weight + $result,'points' => $greenUser->points + $result,'update_time' => time()]);
                                   }else{
                                       GreenUser::create(['member_miniapp_id' => $device->member_miniapp_id,'uid' => $begin[0]->uid,'weight' =>$result,'points' =>$result,'create_time' => time(),'update_time' => time()]);
                                   }
                               }
                           }
                            GreenDevice::where(['device_id' => $device->device_id, ])->update(['type' => 0]);
                        }
                    }else{
                        $this->send($server, $fd['address'], $fd['port'], [0x53, 0x00]);
                    }
                    break;
                //小程序
                case 9:
                    if($this->checkSum($buffer)){
                        //设备
                        $device   = GreenDevice::where(['device_id' => hexdec(implode("", [$buffer[2], $buffer[3]]))])->find();
                        //离线
                        if($device->state == 1){
                            $this->send($server, $fd['address'], $fd['port'], [0x53, 0x40]);
                            break;
                        //箱满
                        }else if($device->danger){
                            $this->send($server, $fd['address'], $fd['port'], [0x53, 0x31]);
                            break;
                        }
                        $data = [
                            'member_miniapp_id' => $device->member_miniapp_id,
                            'device_id'         => $device->device_id, //设备号
                            'uid'               => hexdec(implode("", [$buffer[4], $buffer[5], $buffer[6]])), //用户id
                            'ip'                => $fd['address'],
                            'port'              => $fd['port'],
                            'user_content'      => bin2hex($data),
                            'create_time'       => time(),
                        ];
                        switch ($buffer[7]){
                            //开投递门
                            case 'a1':
                                $data['type'] = 0;
                                $state = GreenDeviceState::where(['member_miniapp_id' => $data['member_miniapp_id'],'device_id' => $data['device_id'],'uid' => $data['uid'],
                                                                  'type' => $data['type'],'state' => 0])->find();
                                if(empty($state)){
                                    GreenDeviceState::create($data);
                                }else{
                                    GreenDeviceState::where(['id' => $state->id])->update($data);
                                }
                                $this->send($server, $device->ip, $device->port, [0x53, 0xA1]);
                                break;
                            //关投递门
                            case 'a2':
                                $data['type'] = 1;
                                $state = GreenDeviceState::where(['member_miniapp_id' => $data['member_miniapp_id'],'device_id' => $data['device_id'],'uid' => $data['uid'],
                                                                  'type' => $data['type'],'state' => 0])->find();
                                if(empty($state)){
                                    GreenDeviceState::create($data);
                                }else{
                                    GreenDeviceState::where(['id' => $state->id])->update($data);
                                }
                                $this->send($server, $device->ip, $device->port, [0x53, 0xB1]);
                                break;
                            //开回收门
                            case 'b1' :
                                $data['type'] = 2;
                                $manage = GreenDevice::where(['member_miniapp_id' =>$device->member_miniapp_id,'device_id' => $device->device_id,'manage_uid' => $data['uid']])->find();
                                if($manage){
                                    $state = GreenDeviceState::where(['member_miniapp_id' => $data['member_miniapp_id'],'device_id' => $data['device_id'],'uid' => $data['uid'],
                                                                      'type' => $data['type'],'state' => 0])->find();
                                    if(empty($state)){
                                        GreenDeviceState::create($data);
                                    }else{
                                        GreenDeviceState::where(['id' => $state->id])->update($data);
                                    }
                                    $this->send($server, $device->ip, $device->port, [0x53, 0xC1]);
                                }
                                break;
                        }
                    }else{
                        $this->send($server, $fd['address'], $fd['port'], [0x53, 0x00]);
                    }
                    break;
            }
        }catch (\Exception $e){
            $this->send($server, $fd['address'], $fd['port'], [0x53, 0x00]);
        }
    }
    //发送消息
    public function send($server, $ip, $port, $array){
        var_dump($array);
        $str = '';
        foreach ($array as $ch) {
            $str .= chr($ch);
        }
        $server->sendto($ip, $port, $str);
    }
    //校验心跳包
    public function checkHeart($buffer){
        if(bin2hex(~hex2bin($buffer[0])) == $buffer[2] && bin2hex(~hex2bin($buffer[1])) == $buffer[3]){
            return hexdec(implode("",[$buffer[0],$buffer[1]]));
        }else{
            return false;
        }
    }
    //校验数据包
    public function checkSum($buffer){
        $sum = 0;
        $data = array_chunk($buffer,count($buffer) - 1);
        foreach ($data[0] as $value) {
            $sum = base_convert( hexdec('0x'.$sum) + hexdec('0x'.$value),10,16);
            if(strlen($sum) >= 3){
                $sum = substr($sum,1,2);
            }
        }
        return (string)$sum == $data[1][0] ? true : false;
    }
    //计算重量
    public function weight($buffer1,$buffer2){
        $weight1 = implode("",[$buffer1[9],$buffer1[10]]);
        $weight2 = implode("",[$buffer2[9],$buffer2[10]]);
        return hexdec(base_convert(((hexdec('0x'.$weight2) - hexdec('0x'.$weight1)) * 0x6),10,16));
    }
}
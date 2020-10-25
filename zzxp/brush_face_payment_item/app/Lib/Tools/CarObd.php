<?php
namespace App\Lib\Tools;

use App\Lib\Tools\Aes;

/*
|------------------
| OBD操作类
|------------------
*/
class CarObd
{
    protected $controller;

    // 接口配置参数
    protected $_obd_host = 'http://221.123.179.91:9819/zzZC/',
              $_obd_customerFlag = '000',
              $_obd_secretKey = '1170tsywzc';

    // 接口返回错误码
    protected $_obd_error = [
        '1001' => '总线忙',
        '1002' => '不支持',
        '1005' => '指令内容时间戳超时',
        '1007' => '无法识别的指令或数据',
        '1010' => '手刹车未拉起，不支持操作',
        '1011' => '车辆行驶中，不支持操作',
        '1012' => '指令执行超时',
        '1014' => '车辆报警，不支持此操作',
        '1017' => '门未关好，不支持此操作',
        '1018' => '车辆点火，不支持此操作',
        '1019' => '车辆熄火，不支持此操作',
        '1020' => '非遥控启动状态，不支持此操作',
        '1022' => '控制盒升级暂不支持此操作',
        '1023' => '设防失败，锁门成功',
        '1024' => '锁门失败（车门未关）',
        '1025' => 'ON状态不执行',
        '1027' => '门未关，锁门失败',
        '1028' => '动作执行前执行失败',
        '1029' => '中控锁未锁',
        '1030' => '开锁后中控锁为锁状态 (锁门不成功)',
        '1094' => '有未执行完控制命令,请稍后重试',
        '1095' => '调用超时',
        '1096' => '暂时不能进行操作',   // 暂时代表不能操作，主程序升级时，也是这个编号
        '1097' => 'IDC不在线',
        '1098' => '调用错误',
        '1103' => '没有找到对应SN',
        '1104' => '接口调用错误',
        '1105' => '车辆发动机未关',
        '1201' => '接口返回为空或失败',
        '1306' => '系统异常',
        '1307' => '控制失败',
        '1308' => '设备不在线',
        '1309' => '设备在线，但无返回信息，可能设备掉线',
        '1311' => '失败，原因未知'
    ];
    // 接口指令说明
    protected $_obd_system = [
        '500' => '开门',
        '501' => '锁门',
        'C00' => '静音开启',
        'C01' => '静音关闭',
        '400' => '远程寻车',
        '100' => '断开总线',
        '101' => '恢复总线',
        'S100' => '断开总线并关门',
        'S101' => '恢复总线并开门',
    ];

    // curl post
    protected function post($curlPost, $url) {
        $t_start = time();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $reponse = curl_exec($curl);

        if ($reponse == false) {
            $log_file = storage_path('logs/obd_err.log');

            $err_info = date("Y-m-d H:i:s")."\tBegin at[".date("Y-m-d H:i:s", $t_start)."]; Run_time[".(time() - $t_start)."];\tErr_no: ".curl_errno($curl)."; Err: ".curl_error($curl)."; Http_code: ".curl_getinfo($curl, CURLINFO_HTTP_CODE)."\r\n";
            @file_put_contents($log_file, $err_info, FILE_APPEND);
        }
        else {
            $reponse = iconv('GB2312', 'UTF-8', $reponse);
        }

        curl_close($curl);

        return $reponse;
    }

    // 生成密钥
    protected function getSecret($param) {
        $secret_string = http_build_query($param);
        $secret_string .= $this->_obd_secretKey;
        return md5($secret_string);
    }

    protected function object_to_array($obj) {
        if (empty($obj)) {
            return [];
        }

        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;

        foreach ($_arr as $key => $val) {
            $val = (is_array($val)) || is_object($val) ? $this->object_to_array($val) : $val;
            $arr[$key] = $val;
        }

        return $arr;
    }

    // 执行，返回结果
    protected function exec($url, $param = []) {
        if (empty($url)) {
            return;
        }

        $reponse = $this->post($param, $url);
        $return = json_decode($reponse);

        if (!isset($return->result)) {
            return ['status'=>'-1','error'=>'操作异常，请稍候再试','return'=>print_r($reponse, true)];
        }

        // 根据接口返回信息
        if ($return->result != 1) {
            $code = $return->result;

            // 错误码解析
            if (isset($this->_obd_error[$code])) {
                $return->message = $this->_obd_error[$code];
            }
            else {
                empty($return->message) && $return->message = '';
                $return->message .= $code;
            }

            // TODO 记录日志
            
            $result = $this->setError($return->message,-2);
            $result['return'] = $return;
            return $result;
        }

        $result = $this->object_to_array($return);

        return $result;  // 配合已有的接口数据格式
    }

    /**
    * 车辆状态数据
    *
    * @Description
    *  (String) sn: OBD设备ID；多个SN以‘,’号隔开
    **/
    public function getStatus($sn) {

        if (empty($sn)) {
            $result = $this->setError('设备编号不能为空，请检查');
            return $result;
        }

        $port_uri = $this->_obd_host.'GetCarsStatus.ashx';
        $port_param = ['customerFlag'=>$this->_obd_customerFlag, 'SN'=>$sn];

        return $this->exec($port_uri, $port_param);
    }
    
    /**
    * 车辆当前GPS数据
    *
    * @Description
    *  (String) sn: OBD设备ID；多个SN以‘,’号隔开
    **/
    public function gpsInfo($sn) {
        // $sn = $this->request->get('sn', '');

        if (empty($sn)) {
            $result = $this->setError('设备编号不能为空，请检查');
            return $result;
        }

        $port_uri = $this->_obd_host.'GetGpsInfo.ashx';
        $port_param = ['customerFlag'=>$this->_obd_customerFlag, 'SN'=>$sn];

        return $this->exec($port_uri, $port_param);
    }

    // 获取毫秒数
    private function getMillisecond($timer=0) {
        if (empty($timer)) {
            list($t1, $t2) = explode(' ', microtime());
        }
        else {
            $t1 = 0;
            $t2 = $timer;
        }

        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    /**
    * 车辆控制(远程指令)
    *
    * @Description
    *  (String) sn: 指定某一个OBD设备ID
    *  (String) code: 设备密码
    *  (String) order_num: 指令值
    **/
    public function remoteOrder($sn,$code,$order_num) {       

        if (empty($sn) || empty($code) || empty($order_num)) {
            $result = $this->setError('参数有误，请检查');
            return $result;
        }

        $port_uri = $this->_obd_host.'RemoteControlCarsNew.ashx';
        $port_param = ['customerFlag'=>$this->_obd_customerFlag, 'SN'=>$sn, 'Code'=>$code, 'Value'=>$order_num, 'TimeStamp'=>$this->getMillisecond()];
        // 获取密钥
        $port_param['checkInfo'] = $this->getSecret($port_param);

        return $this->exec($port_uri, $port_param);
    }

    /**
    * 车辆轨迹获取
    *
    * @Description
    *  (String) SN: OBD设备编号；多个SN以‘,’号隔开
    *  (String) start_time: 开始时间
    *  (String) end_time: 结束时间
    **/
    public function drivingTrack() {
        $sn = $this->request->get('sn', '');
        $start_time = $this->request->get('start_time', 0);
        $end_time = $this->request->get('end_time', 0);

        if (empty($sn) || empty($start_time) || empty($end_time)) {
            $result = $this->setError('请求参数不能为空，请检查');
            return $this->getResult($result, []);
        }

        $port_uri = $this->_obd_host.'GetDrivingTrack.ashx';
        $port_param = ['customerFlag'=>$this->_obd_customerFlag, 'SN'=>$sn, 'startTime'=>$this->getMillisecond(strtotime($start_time)), 'endTime'=>$this->getMillisecond(strtotime($end_time))];

        return $this->exec($port_uri, $port_param);
    }

    /**
    * 密码键盘锁
    *
    * @Description
    *  (String) sn: 指定某一个OBD设备ID
    *  (String) code: 设备密码
    *  (string) pwd_type: 密码类型(1：用户密码，2：管理员密码)
    *  (Int) pwd_code: 解锁密码
    **/
    public function controlPwd($sn, $code, $pwd_type, $pwd_code)
    {
        if (empty($sn) || empty($code) || empty($pwd_type) || empty($pwd_code)) {
            $result = $this->setError('参数有误，请检查');
            return $result;
        }

        $param = [
            'sn' => $sn,
            'code' => $code,
            'pwd' => "#{$pwd_code}#",
            'customerFlag' => $this->_obd_customerFlag,
            'type' => $pwd_type
        ];

        $param = json_encode($param);

        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);

        $aes_tool = new Aes;
        $port_param = [
            'Value' => $aes_tool->encrypt($param),
        ];
        $port_param['checkInfo'] = md5('Value='.$port_param['Value'].$this->_obd_secretKey);

        $port_uri = $this->_obd_host.'SetControlPwd.ashx';

        return $this->exec($port_uri, $port_param);
    }

    //处理错误信息
    private function setError($error,$status = -1){
        return ['status'=>$status,'error'=>$error];
    }

    // 获取设备在线或不在线数据
    public function onlineList($sn) {
        if (empty($sn)) {
            $result = $this->setError('请求参数不能为空，请检查');
            return $this->getResult($result, []);
        }

        $port_uri = $this->_obd_host.'GetOnlineListNew.ashx';
        $port_param = ['customerFlag'=>$this->_obd_customerFlag, 'SN'=>$sn];

        return $this->exec($port_uri, $port_param);
    }
}
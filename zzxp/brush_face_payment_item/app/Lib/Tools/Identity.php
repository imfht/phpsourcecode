<?php
namespace App\Lib\Tools;
use App\Lib\Api\MemberApi;
/*
|------------------
| 身份验证类
|------------------
*/
class Identity
{
    public function __construct(){
        $this->member_api = new MemberApi;
    }
    //检测实名身份证
    public function checkIdentity($idcard,$name){
        $key = env('INDENTITY_KEY','');
        $url = env('INDENTITY_URL','');
        if(empty($key) || empty($url)){
            return $this->setError('配置有误，不能检测');
        }
        $result = $this->request($url,['idCard'=>$idcard,'name'=>$name,'key'=>$key],'POST');

        if($result['code'] == '10000' && $result['message'] == '认证信息匹配'){
            $this->member_api->addDataLog([
                'opera_title'=>'身份实名认证',
                'type'=>1,
                'member_id'=>\Session::get('member_id',0),
                'url'=>json_encode(['idCard'=>$idcard,'name'=>$name,'key'=>$key],JSON_UNESCAPED_UNICODE),
                'status'=>1,
                'error'=>'身份实名认证通过']);
            return $this->setSuccess('身份实名认证通过',1);
        }

        $this->member_api->addDataLog([
            'opera_title'=>'身份实名认证',
            'type'=>1,
            'url'=>json_encode(['idCard'=>$idcard,'name'=>$name,'key'=>$key],JSON_UNESCAPED_UNICODE),
            'status'=>-1,
            'member_id'=>\Session::get('member_id',0),
            'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
        return $this->setError('身份实名认证失败');
    }
    //检测手机号码
    public function checkPhone2($phone,$idcard,$name){
        $key = env('PHONE_NEW_KEY','');
        $url = env('PHONE_NEW_URL','');
        if(empty($key) || empty($url)){
            return $this->setError('配置有误，不能检测');
        }
        if(empty($phone)){
            return $this->setError('电话号码不能为空');
        }
        $param = [
            'key' => $key,
            'idcard'=>$idcard,
            'name'=>$name,
            'phone' => $phone];
            // print_r($param);
            // exit();
        $result = $this->request($url,$param,'POST');
        if($result['code'] == '10000'){
            if($result['data']['result'] == 'T'){

                $this->member_api->addDataLog([
                    'opera_title'=>'手机验证',
                    'type'=>2,
                    'member_id'=>\Session::get('member_id',0),
                    'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                    'status'=>1,
                    'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
                return $this->setSuccess('手机验证通过',1,['type'=>$model,'name'=>$name]);
            }            
        }

        $this->member_api->addDataLog([
            'opera_title'=>'手机验证',
            'type'=>2,
            'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
            'status'=>-1,
            'member_id'=>\Session::get('member_id',0),
            'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
        return $this->setError('手机验证失败');
    }
    //检测手机号码
    public function checkPhone($phone,$idcard,$name){
        $key = env('PHONE_KEY','');
        $url = env('PHONE_URL','');
        if(empty($key) || empty($url)){
            return $this->setError('配置有误，不能检测');
        }
        if(empty($phone)){
            return $this->setError('电话号码不能为空');
        }
        $param = [
            'key' => $key,
            'idNumber'=>$idcard,
            'personName'=>$name,
            'mobileNo' => $phone];
            // print_r($param);
            // exit();
        $result = $this->request($url,$param,'POST');
        if($result['code'] == '10000'){
            $model = 0;
            $name = '';
            if($result['data']['idNamePhoneUnicomCheck'] == 1){
                $model = 1;
                $name = '联通';
            }else if($result['data']['nameCheckResult'] == 0){
                $model = 2;
                $name = '电信';
            }else if($result['data']['idNamePhoneCmccCheck'] == 1){
                $model = 3;
                $name = '移动';
            }
            if($model != 0){
                $param['type'] = $model; 
                $this->member_api->addDataLog([
                    'opera_title'=>'手机验证',
                    'type'=>2,
                    'member_id'=>\Session::get('member_id',0),
                    'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                    'status'=>1,
                    'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
                return $this->setSuccess('手机验证通过',1,['type'=>$model,'name'=>$name]);
            }

        }

        $this->member_api->addDataLog([
            'opera_title'=>'手机验证',
            'type'=>2,
            'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
            'status'=>-1,
            'member_id'=>\Session::get('member_id',0),
            'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
        return $this->setError('手机验证失败');
    }
    
    //检测驾照
    //检测驾照
    public function checkDriver($idcard,$name){
        $key = env('DRIVER_KEY','');
        $url = env('DRIVER_URL','');
        if(empty($key) || empty($url)){
            return $this->setError('配置有误，不能检测');
        }
        $idcard = strtoupper($idcard);
        if(empty($idcard) || empty($name)){
            return $this->setError('驾驶证号与姓名不能同时为空');
        }
        $param = [
            'key' => $key,
            'documentNo'=>$idcard,
            'name'=>$name
        ];
        $result = $this->request($url,$param,'POST');
        if($result['code'] == '10000' && isset($result['data'])){
            $data = $result['data'];
            $name = '';
            $type = 0;
            if($data['driverBaseCheck']['treatResult'] == '1' && $data['driverBaseCheck']['item']['documentNoCheckResult'] == '一致' && $data['driverBaseCheck']['item']['nameCheckResult'] == '一致'){
                $type = 1;
                $name = '正常';

                $this->member_api->addDataLog([
                    'opera_title'=>'驾照验证',
                    'type'=>3,
                    'member_id'=>\Session::get('member_id',0),
                    'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                    'status'=>1,
                    'error'=>'驾照验证通过']);
                return $this->setSuccess('驾照验证通过',1);
            }else{

                if($data['driverBaseCheck']['treatResult'] != '1' ){

                    $this->member_api->addDataLog([
                        'opera_title'=>'驾照验证',
                        'type'=>3,
                        'member_id'=>\Session::get('member_id',0),
                        'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                        'status'=>'-1',
                        'error'=>'驾驶证未查到']);
                    return $this->setErrr('驾驶证未查到');
                }

                if($data['driverBaseCheck']['item']['documentNoCheckResult'] != '一致'){

                    $this->member_api->addDataLog([
                        'opera_title'=>'驾照验证',
                        'type'=>3,
                        'member_id'=>\Session::get('member_id',0),
                        'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                        'status'=>'-1',
                        'error'=>'驾驶证号不存在']);
                    return $this->setError('驾驶证号不存在');
                }

                if($data['driverBaseCheck']['item']['nameCheckResult'] != '一致'){
                    $this->member_api->addDataLog([
                        'opera_title'=>'驾照验证',
                        'type'=>3,
                        'member_id'=>\Session::get('member_id',0),
                        'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                        'status'=>'-1',
                        'error'=>'驾驶人姓名不一致']);
                    return $this->setError('驾驶人姓名不一致');
                }

                $this->member_api->addDataLog([
                        'opera_title'=>'驾照验证',
                        'type'=>3,
                        'member_id'=>\Session::get('member_id',0),
                        'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
                        'status'=>'-1',
                        'error'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);

                return $this->setError('驾驶证异常,原因:'.$data['driverLicenseStatusInfo']['item']['driverLicenseStatusDesc']);
            }

        }

        $this->member_api->addDataLog([
            'opera_title'=>'驾照验证',
            'type'=>3,
            'member_id'=>\Session::get('member_id',0),
            'url'=>json_encode($param,JSON_UNESCAPED_UNICODE),
            'status'=>-2,
            'error'=>json_encode($result,JSON_UNESCAPED_UNICODE)]);
        return $this->setError('驾驶证认证失败');

    }

    public function checkDriver1($idcard,$name){
        $key = env('DRIVER_KEY','');
        $url = env('DRIVER_URL','');
        if(empty($key) || empty($url)){
            return $this->setError('配置有误，不能检测');
        }

        if(empty($idcard) || empty($name)){
            return $this->setError('驾驶证号与姓名不能同时为空');
        }
        $param = [
            'key' => $key,
            'documentNo'=>$idcard,
            'name'=>$name
        ];
        $result = $this->request($url,$param,'POST');
        if($result['code'] == '10000' && isset($result['data'])){
            $data = $result['data'];
            $name = '';
            $type = 0;
            if($data['driverLicenseStatusInfo']['treatResult'] == '1' && $data['driverLicenseStatusInfo']['item']['driverLicenseStatusDesc'] == '正常'){
                $type = 1;
                $name = '正常';
                return $this->setSuccess('驾照验证通过',1);
            }else{
                if($data['driverLicenseStatusInfo']['treatResult'] != '1' ){
                    return $this->setError('驾驶证状态未查到');
                }

                if($data['driverBaseCheck']['treatResult'] != '1' ){
                    return $this->setErrr('驾驶证未查到');
                }

                if($data['driverBaseCheck']['item']['documentNoCheckResult'] != '一致'){
                    return $this->setError('驾驶证号不存在');
                }

                if($data['driverBaseCheck']['item']['nameCheckResult'] != '一致'){
                    return $this->setError('驾驶人姓名不一致');
                }

                return $this->setError('驾驶证异常,原因:'.$data['driverLicenseStatusInfo']['item']['driverLicenseStatusDesc']);
            }

        }
        return $this->setError('驾驶证认证失败');

    }
    //请求接口
    private function request($url,$data,$method = 'GET'){
        $ch = curl_init();
        if($method != 'GET'){
            curl_setopt( $ch , CURLOPT_POST , true );
            @curl_setopt( $ch , CURLOPT_POSTFIELDS , $data);
        }else{
            if(is_array($data)){
                $param = [];
                foreach($data as $k => $v){
                    $param[] = $k .'='.$v;
                }
                $url .= '?'.implode('&',$param);   
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response,true);
    }
    //错误信息
    private function setError($error){
        return ['status'=>-1,'error'=>$error];
    }
    //检测成功信息
    private function setSuccess($msg,$status,$data = []){
        return ['status'=>$status,'msg'=>$msg,'data'=>$data];
    }
}
?>
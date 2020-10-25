<?php
use Common\Api\SmsApi;
use Lib\Curl;
//互亿无线
class IhuyiSms extends SmsApi{

	private $api_url='http://106.ihuyi.cn/webservice/sms.php';

//发送已备案的模板短信
/*
<?xml version="1.0" encoding="utf-8"?>
<SubmitResult xmlns="http://106.ihuyi.cn/">
<code>2</code>
<msg>提交成功</msg>
<smsid>101056283</smsid>
</SubmitResult>
*/
	public function send($mobile='',$content=''){
		if(empty($mobile) || empty($content)){
			$this->error='手机号码和内容不能为空';
			return false;
		}
		$url = $this->api_url.'?method=Submit';

		$data=array();
		$data['account']=$this->username;
		$data['password']=$this->password;
		$data['content']=trim($content);
		$data['mobile']=$mobile;

		$curl = new Curl();
		$result_xml = $curl->post($url,$data);
		$result_arr = $this->xmlToArray($result_xml);
		if($result_arr['SubmitResult']['code'] == '2'){
			return ture;
		}else{
			$this->error=$result_arr['SubmitResult']['msg'];
			return false;
		}
	}

//查询剩余短信条数
/*
<?xml version="1.0" encoding="utf-8"?>
<GetNumResult xmlns="http://106.ihuyi.cn/">
<code>2</code>
<msg>查询成功</msg>
<num>7</num>
</GetNumResult>
*/
	public function query(){
		$url = $this->api_url.'?method=GetNum';

		$data=array();
		$data['account']=$this->username;
		$data['password']=$this->password;

		$curl = new Curl();
		$result_xml = $curl->post($url,$data);
		$result_arr = $this->xmlToArray($result_xml);
		if($result_arr['GetNumResult']['code'] == '2'){
			return $result_arr['GetNumResult']['num'];
		}else{
			$this->error=$result_arr['SubmitResult']['msg'];
			return false;
		}
	}

//测试发送验证码
/*
<?xml version="1.0" encoding="utf-8"?>
<SubmitResult xmlns="http://106.ihuyi.cn/">
<code>2</code>
<msg>提交成功</msg>
<smsid>101056283</smsid>
</SubmitResult>
*/
	public function test($mobile=''){
		if(empty($mobile)){
			$this->error='手机号码不能为空';
			return false;
		}
		$url = $this->api_url.'?method=Submit';
		$rand_number=rand_string(6,1);

		$data=array();
		$data['account']=$this->account;
		$data['password']=$this->password;
		$data['content']=trim("您的验证码是：".$rand_number."。请不要把验证码泄露给其他人。");
		$data['mobile']=$mobile;

		$curl = new Curl();
		$result_xml = $curl->post($url,$data);
		$result_arr = $this->xmlToArray($result_xml);
		if($result_arr['SubmitResult']['code'] == '2'){
			return ture;
		}else{
			$this->error=$result_arr['SubmitResult']['msg'];
			return false;
		}
	}

    private function xmlToArray($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = $this->xmlToArray( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
}
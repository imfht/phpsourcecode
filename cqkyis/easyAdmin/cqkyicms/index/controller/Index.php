<?php
namespace app\index\controller;

use service\AliyunService;
use think\Controller;
use think\facade\Session;

class Index extends Controller
{
    public function index()
    {
        //return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
      //return rand_string(4,1);
        return "fdsafa";
    }

    public function sendSms()
    {
        $tel='15215251247';
        $code = rand(1000,9999);
        $Aliyun = new AliyunService();
        $res = $Aliyun->sendsms($tel,$code,'SMS_118215001');
        if($res['msg'] == 'OK'){
            return $this->response(200,'发送成功!');
        }else{
            return $this->response(400,'发送失败!');
        }
    }

    public function worker(){
        return json(rand_string(4,1)) ;
        //return $this->fetch('/worker');
    }

    public function test(){
       return json(rand_string(4,1)) ;
    }


    public function se(){
        $code = rand_string(4,1);
        Session::set('ss',$code);
        $this->demo();
    }
    public function demo(){
        dump(Session::get('ss'));
    }

}

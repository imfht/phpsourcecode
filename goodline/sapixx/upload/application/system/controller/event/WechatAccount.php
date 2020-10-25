<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信服务号-扫码登录处理
 */
namespace app\system\controller\event;
use think\Controller;
use app\common\event\Passport;
use app\common\facade\WechatMp;
use app\common\model\SystemMember;
use app\common\model\SystemWeb;
use think\facade\Cookie;
use filter\Filter;
use Exception;

class WechatAccount extends Controller{

    /**
     *  发起微信授权
     */
    public function index(){
        try {
            $app = WechatMp::official();
            $app->server->push(function ($message) {
                switch ($message['MsgType']) {
                    case 'event':
                        $open_id['appid']  = Filter::filter_escape($message['FromUserName']);  //openid
                        if($message['Event'] == 'subscribe' || $message['Event'] == 'SCAN'){ //关注|扫描带参数二维码事件
                            $member = SystemMember::where(['open_id' => $open_id['appid']])->field('open_id')->find();
                            if($member){
                                $member->ticket = Filter::filter_escape($message['Ticket']);  //Ticket
                                $member->save();
                                return '你好,你刚刚登录了「'.SystemWeb::config()['name'].'」';
                            }else{
                                //创建用户
                                $rdm_code = getcode(6);
                                $data['appid']         = $open_id['appid'];
                                $data['ticket']        = Filter::filter_escape($message['Ticket']); 
                                $data['username']      = '公众号'.$rdm_code;
                                $data['password']      = password_hash(md5($rdm_code),PASSWORD_DEFAULT);
                                $data['safe_password'] = password_hash(md5('123456'),PASSWORD_DEFAULT); 
                                $data['login_time']    = time();
                                $data['login_ip']      = request()->ip();
                                $data['update_time']   = time();
                                $data['create_time']   = time();
                                SystemMember::create($data);
                                return '你好,你刚刚注册了「'.SystemWeb::config()['name'].'」帐号';
                            }
                        }
                        break;
                    default:
                        return '';
                        break;
                }
            });
            $response = $app->server->serve();
            $response->send();
        }catch (Exception $e) {
            $this->error('授权失败');
        }
    }
 
    /**
     *  生成登录二维码
     */
    public function createCodes(){
        try {
            $rel = WechatMp::official()->qrcode->temporary('foo', 6 * 24 * 3600);
            Cookie::set('login_ticket',$rel['ticket'],$rel['expire_seconds']);  //记录二维码ticket
            return enjson(200,'成功',$rel['url']);
        }catch (Exception $e) {
            return enjson(0);
        }
    }

    /**
     *检查登录状态
     */
    public function qrCodes(){
        if(Cookie::has('login_ticket')){
            $ticket = Cookie::get('login_ticket');
            $member  = SystemMember::where(['ticket' => $ticket])->field('id,username,open_id,ticket')->find();
            if($member){
                $member->ticket = null;  //Ticket
                $member->save();
                Passport::clearMiniapp();
                Passport::setlogout();
                Passport::setLogin($member);
                Cookie::delete('login_ticket');
                return enjson(200,'成功',['url' => url('system/passport.Index/index')]); 
            }
        }
        return enjson(0); 
    }
}
<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信第三方开放平台开放-接入
 */
namespace app\system\controller\event;
use think\Controller;
use app\common\facade\WechatMp;
use app\common\facade\WechatProgram;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberKeyword;
use app\common\model\SystemMemberMiniappToken;
use EasyWeChat\OpenPlatform\Server\Guard;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Media;
use think\facade\Log;
use Exception;

class WechatOpen extends Controller{

    /**
     * 微信开放平台推送车票(1次/10分钟)
     * 有了车票要保存下来,获取授权时要用
     * @return json
     */
    public function ticket(){
        try {
            $server = WechatMp::openConfig()->server;
            // 处理授权成功事件
            $server->push(function ($message) {
                Log::write($message,'EVENT_AUTHORIZED');
            }, Guard::EVENT_AUTHORIZED);
            // 处理授权更新事件
            $server->push(function ($message) {
                Log::write($message,'EVENT_UPDATE_AUTHORIZED');
            }, Guard::EVENT_UPDATE_AUTHORIZED);
            //处理授权取消事件
            $server->push(function ($message) {
                SystemMemberMiniappToken::where(['authorizer_appid' => $message['AuthorizerAppid']])->delete();
                Log::write($message,'EVENT_UNAUTHORIZED');
            }, Guard::EVENT_UNAUTHORIZED);
            $server->serve();
            return response("success");
        }catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * 微信开放平台事件接受
     * @return json
     */
    public function message($appid){
        try {
            //公众号和小程序开放平台接入验证
            if ($appid == 'wx570bc396a51b8ff8' || $appid == 'wxd101a85aa106f53e') {
                $app = $appid == 'wx570bc396a51b8ff8' ? WechatMp::openConfig()->officialAccount($appid) : WechatMp::openConfig()->miniProgram($appid);
                $app->server->push(function ($message) {
                    switch ($message['MsgType']) {
                        case 'event':
                            return $message['Event'].'from_callback';
                            break;
                        case 'text':
                            if ($message['Content'] == "TESTCOMPONENT_MSG_TYPE_TEXT") {
                                return 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
                            } else {
                                $authCode = explode(":",$message['Content'])[1];
                                return new Text($authCode."_from_api");
                            }
                            break;
                        default;
                            return new Text("wecaht open account verify");
                            break;
                    }
                });
            } else {
                $miniapp = SystemMemberMiniapp::whereOr(['miniapp_appid' => $appid,'mp_appid' => $appid])->field('id,miniapp_appid,mp_appid')->find();
                if(empty($miniapp)){
                    return response("fail");
                }
                $member_miniapp_id = $miniapp->id;
                $is_miniapp = 0;
                if($miniapp->mp_appid == $appid){
                    $app = WechatMp::isTypes($member_miniapp_id);
                }else{
                    $app = WechatProgram::isTypes($member_miniapp_id);
                    $is_miniapp = 1;
                }
                $app->server->push(function($message) use ($member_miniapp_id,$is_miniapp){
                    if($message['MsgType'] == 'event'){
                        $keyword = isset($message['EventKey']) ? $message['EventKey'] : $message['Event'];
                    }else{
                        $keyword = $message['Content'];
                    }
                    $rel = SystemMemberKeyword::where(['member_miniapp_id' => $member_miniapp_id,'is_miniapp' => $is_miniapp,'keyword' => $keyword])->find();
                    if(empty($rel)){
                        return;
                    }
                    switch ($rel->type) {
                        case 'link':
                            $item = ['title' => $rel->title,'description' => $rel->content,'url' => $rel->url];
                            if($is_miniapp){
                                $item['thumb_url'] = $rel->image;
                            }else{
                                $item['image'] = $rel->image;
                            }
                            $items = [new NewsItem($item)];
                            $msg = new News($items);
                            break;
                        case 'image':
                            $msg = new Image($rel->media_id);
                            break;
                        case 'media':
                            $msg = new Media($rel->media_id,'mpnews'); //mpnews、mpvideo、voice、image
                            break;
                        default:
                            $msg = new Text($rel->content);
                            break;
                    }
                    if($is_miniapp){
                        WechatProgram::isTypes($member_miniapp_id)->customer_service->message($msg)->to($message['FromUserName'])->send();
                    }else{
                        return $msg;
                    }
                });
            }
            $app->server->serve()->send();
        }catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
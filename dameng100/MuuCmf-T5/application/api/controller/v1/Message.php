<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use Firebase\JWT\JWT;
use app\api\controller\Api;
use app\api\controller\UnauthorizedException;
use app\api\controller\v1\Base;
use think\Db;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 */
class Message extends Base
{   
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';
    public $apiAuth = false;

    
    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */
    public function index()
    {   
        //验证权限
        if($this->checkAccessToken() !== true){
            return $this->sendError($this->checkAccessToken());
        }

        //测试发送消息
        //$order = model('knowledge/KnowledgeOrders')->getDataByOrderNO('201904185799985248');
        //$order = $order->toArray();
        //$title = '课程购买';
        //$content = '你成功购买了课程'.'【'.$order['order_info']['title'].'】';
        //model('common/Message')->sendMessage($this->uid, $title, $content, '', '',1,'knowledge', 2);

        $action = input('action','list','text');

        switch($action){
            case 'list':
                //消息类型
                $type = input('post.type','','text');
                
                $map['to_uid'] = $this->uid;
                if(!empty($type)){
                    $map['type'] = $type;
                }
                $messages = Db::name('Message')->where($map)->order('create_time desc')->paginate(10);
                $messages = $messages->toArray()['data'];

                foreach ($messages as &$v) {
                    model('common/Message')->readMessage($v['id']);//设置这个消息为已读
                    $v['content'] = model('common/Message')->getContent($v['content_id']);
                    if ($v['from_uid'] != 0) {
                        $v['from_user'] = query_user(['nickname', 'space_url', 'avatar64', 'space_link'], $v['from_uid']);
                    }
                    if($v['content']['url']) {
                        if(preg_match('/^(http|https).*$/',$v['content']['url'])){
                            $v['from_url']=$v['content']['url'];
                        }else{
                            $v['from_url']=$v['content']['url'];
                        };
                    }
                }
                unset($v);

                return $this->sendSuccess('success',$messages);

            break;

            case 'type':
                //消息类型
                $type = input('post.type','','text');
                $messageType = model('common/Message')->getMyMessageTypeList($this->uid);

                return $this->sendSuccess('success',$messageType);
            break;
        }
    }

    /**
     * post方式
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //获取新消息推送
        //验证权限
        if($this->checkAccessToken() !== true){
            return $this->sendError($this->checkAccessToken());
        }

        //取到所有没有提示过的信息
        $message_count = model('common/Message')->getHaventReadMessageCount($this->uid);
        $haventToastMessages = model('common/Message')->getHaventToastMessage($this->uid);
        //读取到推送之后，自动删除此推送来防止反复推送。
        model('common/Message')->setAllToasted($this->uid);
        //消息中心推送
        
        return $this->sendSuccess('success',['message_count'=>$message_count,'messages' => $haventToastMessages]);
    }

    /**
     * get方式
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {   
        
    }
}


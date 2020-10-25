<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 想天
 * 创建日期: 3/12/14
 * 创建时间: 12:49 PM
 * 版权所有 想天工作室(www.ourstu.com)
 */

namespace Ucenter\Controller;

use Think\Controller;

class MessageController extends BaseController
{
    protected $mTalkModel;

    public function _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {

    }

    /**消息页面
     * @param int $page
     * @param string $tab 当前tab
     */
    public function message($page = 1,$r = 20 ,$tab = 'unread')
    {
        $tpl=$this->_messageTpl($tab);
        //dump($tpl);exit;
        //$tplcode = file_get_contents($tpl);
        //从条件里面获取Tab
        //$map = $this->getMapByTab($tab);
        $map['to_uid'] = is_login();
        $map['type'] = $tab;

        $messages = D('Message')->where($map)->order('create_time desc')->page($page, $r)->select();
        $totalCount = D('Message')->where($map)->order('create_time desc')->count(); //用于分页

        foreach ($messages as &$v) {
            D('Common/Message')->readMessage($v['id']);//设置这个消息为已读
            $v['content'] = D('Common/Message')->getContent($v['content_id']);
            if ($v['from_uid'] != 0) {
                $v['from_user'] = query_user(array('nickname', 'space_url', 'avatar64', 'space_link'), $v['from_uid']);
            }
            if($v['content']['url']) {
                $model = explode('/',$v['content']['url']);
                $v['module']=ucwords($model[0]);
                $map=json_decode($v['content']['args_json']);
                $from = M($v['module'])->where($map)->find();
                $v['from']=$from;
            }
            $v['tpl']=$tpl;
        }
        unset($v);

        //获取含TAB的消息类型详情
        $messageType = D('Common/Message')->getInfo($tab);
        //获取用户消息类型列表
        $messageTypeList= D('Common/Message')->getMyMessageTypeList();

        $this->assign('tpl',$tpl);
        $this->assign('totalCount', $totalCount);
        $this->assign('messages', $messages);
        $this->assign('messageType',$messageType);
        $this->assign('messageTypeList',$messageTypeList);
        //dump($message);exit;

        //设置Tab
        $this->defaultTabHash('message');
        $this->assign('tab', $tab);
        $this->display();
    }

    /**
     * ajax消息列表
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function messageList($page = 1,$r = 10)
    {
        if(POST){
            $page = I('post.page',1,'intval');
            $r = I('post.r',10,'intval');
            $tab = I('post.tab','','op_t');
            $tpl=$this->_messageTpl($tab);
            $map['to_uid'] = is_login();
            $map['type'] = $tab;

            $messages = D('Message')->where($map)->order('create_time desc')->page($page, $r)->select();
            $totalCount = D('Message')->where($map)->order('create_time desc')->count(); //用于分页

            foreach ($messages as &$v) {
                D('Common/Message')->readMessage($v['id']);//设置这个消息为已读
                $v['content'] = D('Common/Message')->getContent($v['content_id']);
                if ($v['from_uid'] != 0) {
                    $v['from_user'] = query_user(array('nickname', 'space_url', 'avatar64', 'space_link'), $v['from_uid']);
                }
                if($v['content']['url']) {
                    if(preg_match('/^(http|https).*$/',$v['content']['url'])){
                        $v['from']=$v['content']['url'];
                    }else{
                        if($v['content']['args']){
                            $model = explode('/',$v['content']['url']);
                            $v['module']=ucwords($model[0]);
                            $map=json_decode($v['content']['args']);
                            $from = M($v['module'])->where($map)->find();
                            if($from){
                                $v['fromArray']=$from;
                            }
                        }else{
                            $v['from']=U($v['content']['url']);
                        }
                    };
                }
                $v['tpl']=$tpl;
            }
            unset($v);
            //dump($messages);exit;
            //获取含TAB的消息类型详情
            $messageType = D('Common/Message')->getInfo($tab);
            //获取用户消息类型列表
        }
        

        $this->assign('tpl',$tpl);
        $this->assign('totalCount', $totalCount);
        $this->assign('messages', $messages);
        $this->assign('messageType',$messageType);
        //dump($messageTypeList);exit;

        //设置Tab
        $this->defaultTabHash('message');
        $this->assign('tab', $tab);
        $this->display();
    }

    /**回复的时候调用，通过该函数，会回调应用对应的postMessage函数实现对原始内容的数据添加。
     * @param $content 内容文本
     * @param $talk_id 聊天ID
     */
    public function postMessage($content, $talk_id)
    {
        $content = op_t($content);
        //空的内容不能发送
        if (!trim($content)) {
            $this->error(L('_ERROR_CHAT_CONTENT_EMPTY_'));
        }

        D('TalkMessage')->addMessage($content, is_login(), $talk_id);
        $talk = D('Talk')->find($talk_id);
        $message = D('Message')->find($talk['message_id']);

        if ($talk['appname'] != '') {
            $messageModel = $this->getMessageModel($message);

            $messageModel->postMessage($message, $talk, $content, is_login());
        }
        exit(json_encode(array('status' => 1, 'content' => parse_expression($content))));
        $this->success(L('_SUCCESS_SEND_'));
    }
    /**Ajax私信发送处理
    */
    public function postiMessage()
    {
        $to_uid = I('post.iMessageUid',0,'intval');
        $content=I('post.iMessageTxt','','text');
        if (!is_login()) {
            $this->ajaxReturn(array('status' => 0, 'info' => L("_PLEASE_")." ".L("_LOG_IN_")));
        }

        if (!trim($content)) {
            $this->ajaxReturn(array('status' => 0, 'info' => "内容不能为空"));
        }
        $user = query_user(array('id', 'nickname', 'space_url'));
        $message = D('Common/Message')->sendMessage($to_uid, '你有一封私信', $user['nickname'].':' . $content, 'Ucenter/Index/index', array('uid' => is_login()));
        if ($message) {
            $this->ajaxReturn(array('status' => 1, 'info' => "发送" .L('_SUCCESS_')));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => "发送 ".L("_FAIL_")));
        }
    }


    /**
     * @param $message
     * @return \Model
     */
    private function getMessageModel($message)
    {
        $appname = ucwords($message['appname']);
        $messageModel = D($appname . '/' . $appname . 'Message');
        return $messageModel;
    }
    /**
     * 获取用户消息类型列表
     * @return [type] [description]
     */
    public function messageTypeList()
    {
        $messageModel=D('Common/Message');
        $messageTypeList=$messageModel->getMyMessageTypeList();
        $this->assign('message_type_list',$messageTypeList);
        $type_tpl=modC('MESSAGE_TYPE_TPL','type3','Message');
        $this->display(T('Application://Ucenter@default/Message/msg_type_tpl/'.$type_tpl));
    }

    /**
     * 获取某个类型消息的模板
     * @param $message_type
     **/
    private function _messageTpl($message_type)
    {
        $messageTpl=get_all_message_type();//获取所有消息类型

        foreach($messageTpl as $val){
            if($val['name']==$message_type){
                if($val['tpl_name']){
                    $tpl=APP_PATH.$val['module'].'/View/default/MessageTpl/'.$val['tpl_name'].'.html';
                }else{
                    $tpl=APP_PATH.'Common/View/default/MessageTpl/_message_li.html';
                }
            }
        }
        return $tpl;
    }

}
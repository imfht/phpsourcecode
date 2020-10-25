<?php
namespace app\ucenter\controller;

use think\Controller;
use think\Db;
use app\ucenter\controller\Base;

class Message extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**检测消息
     * 返回系统的消息
     */
    public function getInformation()
    {
        //取到所有没有提示过的信息
        $message_count = model('common/Message')->getHaventReadMessageCount(is_login());
        $haventToastMessages = model('common/Message')->getHaventToastMessage(is_login());

        //读取到推送之后，自动删除此推送来防止反复推送。
        model('common/Message')->setAllToasted(is_login()); 
        //消息中心推送
        exit(json_encode(['message_count'=>$message_count,'messages' => $haventToastMessages]));
    }

    /**设置全部的系统消息为已读
     */
    public function setAllMessageReaded()
    {
        model('Message')->setAllReaded(is_login());
    }

    /**设置某条系统消息为已读
     * @param $message_id
     */
    public function readMessage($message_id)
    {
        exit(json_encode(array('status' => model('common/Message')->readMessage($message_id))));

    }
    /**
     * ajax消息列表
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function messageList()
    {
        $tab = input('post.tab','','text');
        $tpl=$this->_messageTpl(strtolower($tab));
        $map['to_uid'] = is_login();
        $map['type'] = $tab;

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
            $v['tpl']=$tpl;
        }
        unset($v);

        //获取含TAB的消息类型详情
        $messageType = model('common/Message')->getInfo($tab);
        //获取用户消息类型列表

        $this->assign('tpl',$tpl);
        $this->assign('messages', $messages);
        $this->assign('messageType',$messageType);

        //设置Tab
        $this->assign('tab', $tab);
        return $this->fetch();  
    }

    /**Ajax私信发送处理
    */
    public function postiMessage()
    {
        $to_uid = input('post.iMessageUid',0,'intval');
        $content=input('post.iMessageTxt','','text');
        if (!is_login()) {
            return json(['code' => 0, 'msg' => lang("_PLEASE_")." ".lang("_LOG_IN_")]);
        }

        if (!trim($content)) {
            return json(['code' => 0, 'msg' => "内容不能为空"]);
        }
        $user = query_user(['id', 'nickname', 'space_url']);
        //发送消息
        $message = model('common/Message')->sendMessage($to_uid, '你有一封私信', $user['nickname'].':' . $content, 'ucenter/Index/index', array('uid' => is_login()));
        if ($message) {
            return json(['code' => 200, 'msg' => "发送" .lang('_SUCCESS_')]);
        } else {
             return json(['code' => 0, 'msg' => "发送 ".lang("_FAIL_")]);
        }
    }

    /**
     * 获取用户消息类型列表
     * @return [type] [description]
     */
    public function messageTypeList()
    {
        $messageTypeList=model('common/Message')->getMyMessageTypeList();
        $this->assign('message_type_list',$messageTypeList);
        $type_tpl=modC('MESSAGE_TYPE_TPL','type3','Message');
        return $this->fetch('ucenter@message/msg_type_tpl/'.$type_tpl);
    }

    /**
     * 获取某个类型消息的模板
     * @param $message_type
     **/
    private function _messageTpl($message_type='common_system')
    {     
        $messageTpl=model('common/Message')->getAllMessageType();//获取所有消息类型
        
        if(empty($message_type) || $message_type=='') $message_type='common_system';

        $tpl=APP_PATH.$messageTpl[strtolower($message_type)]['module'].'/view/message/'.$messageTpl[strtolower($message_type)]['tpl_name'].'.html';

        if(file_exists($tpl)){
            return $tpl;
        }else{
            $tpl=APP_PATH.'common/view/message/_message_li.html';
        }
        return $tpl;
    }

}
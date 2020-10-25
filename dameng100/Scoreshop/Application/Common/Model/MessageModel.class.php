<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 想天
 * 创建日期: 3/13/14
 * 创建时间: 7:41 PM
 * 版权所有 想天工作室(www.ourstu.com)
 */

namespace Common\Model;

use Think\Model;

class MessageModel extends Model
{


    /**
     * sendMessage   发送消息，屏蔽自己
     * @param $to_uids 接收消息的用户们
     * @param string $title 消息标题
     * @param string $content 消息内容
     * @param string $url 消息指向的路径，U函数的第一个参数
     * @param array $url_args 消息链接的参数，U函数的第二个参数
     * @param int $from_uid 发送消息的用户
     * @param string $type 消息类型标识，对应各模块message_config.php中设置的消息类型
     * @param string $tpl 消息模板标识，对应各模块message_config.php中设置的消息模板
     * @return bool
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendMessage($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 'Common_system', $tpl = '')
    {
        $from_uid == -1 && $from_uid = is_login();
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);
        $k = array_search($from_uid, $to_uids);
        if ($k !== false) {
            unset($to_uids[$k]);
        }
        if (count($to_uids) > 0) {
            return $this->sendMessageWithoutCheckSelf($to_uids, $title, $content, $url, $url_args, $from_uid, $type,$tpl);
        } else {
            return false;
        }
    }

    /**
     * sendMessageWithoutCheckSelf  发送消息，不屏蔽自己
     * @param $to_uids 接收消息的用户们
     * @param string $title 消息标题
     * @param string $content 消息内容
     * @param string $url 消息指向的路径，U函数的第一个参数
     * @param array $url_args 消息链接的参数，U函数的第二个参数
     * @param int $from_uid 发送消息的用户
     * @param string $type 消息类型标识，对应各模块message_config.php中设置的消息类型
     * @param string $tpl 消息模板标识，对应各模块message_config.php中设置的消息模板
     * @return bool
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendMessageWithoutCheckSelf($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 'Common_system', $tpl = '')
    {
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);
        $to_uids=$this->_removeOldUser($to_uids);
        if(!count($to_uids)){
            return false;
        }
        if (in_array($type, array('1', '2','3','0','4', '5'))) {
            $type = 'Common_system';
        }
        $from_uid == -1 && $from_uid = is_login();
        $message_content_id = $this->addMessageContent($from_uid, $title, $content, $url, $url_args, $type);
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);

        $this->_initUserMessageSession($to_uids, $type);

        foreach ($to_uids as $to_uid) {
            $message['to_uid'] = $to_uid;
            $message['content_id'] = $message_content_id;
            $message['from_uid'] = $from_uid;
            $message['create_time'] = time();
            $message['type'] = $type;
            $message['status'] = 1;
            $message['tpl'] = $tpl;
            $res = $this->add($message);
            unset($message);
        }
        return true;
    }

    /**
     * sendALotOfMessageWithoutCheckSelf  发送很多消息，不屏蔽自己（用于公告发送消息）
     * @param $to_uids 接收消息的用户们
     * @param string $title 消息标题
     * @param string $content 消息内容
     * @param string $url 消息指向的路径，U函数的第一个参数
     * @param array $url_args 消息链接的参数，U函数的第二个参数
     * @param int $from_uid 发送消息的用户
     * @param string $type 消息类型标识，对应各模块message_config.php中设置的消息类型
     * @param string $tpl 消息模板标识，对应各模块message_config.php中设置的消息模板
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function sendALotOfMessageWithoutCheckSelf($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 'Common_system', $tpl = '')
    {
        if (in_array($type, array('1', '2','3','0','4', '5'))) {
            $type = 'Common_system';
        }
        $from_uid == -1 && $from_uid = is_login();
        $message_content_id = $this->addMessageContent($from_uid, $title, $content, $url, $url_args, $type);
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);

        $count=count($to_uids);
        $i=0;
        $num=100;//每次插入100条
        do{
            $do_to_uids=array_slice($to_uids,$i*$num,$num);
            $this->_initUserMessageSession($do_to_uids, $type);
            $dataList=array();
            foreach ($do_to_uids as $to_uid) {
                $message['to_uid'] = $to_uid;
                $message['content_id'] = $message_content_id;
                $message['from_uid'] = $from_uid;
                $message['create_time'] = time();
                $message['type'] = $type;
                $message['status'] = 1;
                $message['tpl'] = $tpl;
                $dataList[]=$message;
                unset($message);
            }
            unset($to_uid);
            $this->addAll($dataList);
            unset($dataList);
            $count-=$num;
            $i=$i+1;
        }while($count>0);
        return true;
    }

    public function sendEmail($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 'Common_email', $tpl = '')
    {
        $from_uid == -1 && $from_uid = is_login();
        $message_content_id = $this->addMessageContent($from_uid, $title, $content, $url, $url_args, $type);
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);
        $result=true;
        $count = count($to_uids);
        $i = 0;
        $num = 100;//每次插入100条
        do {
            $do_to_uids = array_slice($to_uids, $i * $num, $num);
            $this->_initUserMessageSession($do_to_uids, $type);
            $dataList = array();
            foreach ($do_to_uids as $to_uid) {
                $user=query_user('mobile,email',$to_uid);
                //判断用户是否填写了email
                if($user['email']){
                    if(!empty($url)){
                        $emailContent=$content.'<a href='.$url.'>'.$url.'</a>';
                    }else{
                        $emailContent=$content;
                    }
                    $res=send_mail($user['email'],$title,$emailContent);
                    if($res===true){
                        $message['to_uid'] = $to_uid;
                        $message['content_id'] = $message_content_id;
                        $message['from_uid'] = $from_uid;
                        $message['create_time'] = time();
                        $message['type'] = $type;
                        $message['status'] = 1;
                        $message['tpl'] = $tpl;
                        $dataList[] = $message;
                        unset($message);
                    }else{
                        $result=$res;
                    }
                }
            }
            unset($to_uid);
            $this->addAll($dataList);
            unset($dataList);
            $count -= $num;
            $i = $i + 1;
        } while ($count > 0);
        return $result;
    }


    public function sendMobileMessage($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 'Common_mobile', $tpl = '')
    {
        $from_uid == -1 && $from_uid = is_login();
        $message_content_id = $this->addMessageContent($from_uid, $title, $content, $url, $url_args, $type);
        $to_uids = is_array($to_uids) ? $to_uids : array($to_uids);

        $count = count($to_uids);
        $i = 0;
        $num = 100;//每次插入100条
        $result=true;
        do {
            $do_to_uids = array_slice($to_uids, $i * $num, $num);
            $this->_initUserMessageSession($do_to_uids, $type);
            $dataList = array();
            foreach ($do_to_uids as $to_uid) {
                $user=query_user('mobile,email',$to_uid);
                //判断手机号码
                if($user['mobile']){
                    $mobileContent=$content;

                    $res=sendSMS($user['mobile'],$moblieContent);
                    if($res===true){
                        $message['to_uid'] = $to_uid;
                        $message['content_id'] = $message_content_id;
                        $message['from_uid'] = $from_uid;
                        $message['create_time'] = time();
                        $message['type'] = $type;
                        $message['status'] = 1;
                        $message['tpl'] = $tpl;
                        $dataList[] = $message;
                        unset($message);
                    }else{
                        $result=$res;
                    }
                }
            }
            unset($to_uid);
            $this->addAll($dataList);
            unset($dataList);
            $count -= $num;
            $i = $i + 1;
        } while ($count > 0);
        return $result;
    }

    /**
     * 去除一个月没有登录的用户
     * @param $to_uids
     * @return array
     * @author:zzl(郑钟良) zzl@ourstu.com
     */
    private function _removeOldUser($to_uids)
    {
        $map['uid']=array('in',$to_uids);
        $map['status']=1;
        $map['last_login_time']=array('gt',get_time_ago('month'));
        $uids=M('Member')->where($map)->field('uid')->select();
        $uids=array_column($uids,'uid');
        return $uids;
    }
    /**
     * 初始化用户会话类型，没有的补上
     * @param $uids
     * @param $type
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _initUserMessageSession($uids, $type)
    {
        $messageTypeModel = M('MessageType');
        $map['uid'] = array('in', $uids);
        $map['type'] = $type;
        $already_uids = $messageTypeModel->where($map)->field('uid')->select();

        $already_uids = array_column($already_uids, 'uid');

        $need_uids = array_diff($uids, $already_uids);
        if ($already_uids == null)
            $already_uids = array();
        $need_uids = array_diff($uids, $already_uids);
        $dataList = array();
        foreach ($need_uids as $val) {
            S('MY_MESSAGE_SESSION_' . $val, null);
            $dataList[] = array('uid' => $val, 'type' => $type, 'status' => 1);
        }
        unset($val);

        if (count($dataList)) {
            $messageTypeModel->addAll($dataList);
        }
        return true;
    }

    /**
     * addMessageContent  添加消息内容到表
     * @param $from_uid 发送消息的用户
     * @param $title 消息的标题
     * @param $content 消息内容
     * @param $url 消息指向的路径，U函数的第一个参数
     * @param $url_args 消息链接的参数，U函数的第二个参数
     * @param string $type 消息类型，对应各模块message_config.php中设置的会话类型
     * @return mixed
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    private function addMessageContent($from_uid, $title, $content, $url, $url_args, $type)
    {
        $data_content['from_id'] = $from_uid;
        $data_content['title'] = $title;
        $data_content['content'] = is_array($content) ? json_encode($content) : $content;
        $data_content['url'] = $url;
        $data_content['args'] = empty($url_args) ? '' : json_encode($url_args);
        $data_content['type'] = $type;
        $data_content['create_time'] = time();
        $data_content['status'] = 1;
        $message_id = D('message_content')->add($data_content);
        return $message_id;
    }

    /**
     * 根据id获取消息
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getContent($id)
    {
        $content = S('message_content_' . $id);
        if (empty($content)) {
            $content = D('message_content')->find($id);
            if ($content) {
                $content['args'] = json_decode($content['args'], true);
                $content['args_json'] = json_encode($content['args']);
                if ($content['url']) {
                    $content['web_url'] = is_bool(strpos($content['url'], 'http://')) ? U($content['url'], $content['args']) : $content['url'];
                } else {
                    $content['web_url'] = '';
                }
                if (!is_null(json_decode($content['content']))) {
                    $content['content'] = json_decode($content['content'], true);
                    foreach ($content['content'] as &$val) {
                        $val = html($val);
                    }
                    unset($val);
                    $content['tip']=$content['title'];
                }else{
                    $content['tip']=$content['content'];
                }
                if (in_array($content['type'], array('1', '2','3','0','4', '5'))) {
                    $content['type'] = 'Common_system';
                }
            }
            S('message_content_' . $id, $content, 60 * 60);
        }

        return $content;
    }
    /**获取全部未读消息数
     * @param $uid 用户ID
     * @return mixed
     */
    public function getHaventReadMessageCount($uid)
    {
        $count = $this->where('to_uid=' . $uid . ' and  is_read=0')->count();
        return $count;
    }
    /**设置某消息为已读
    * @param $message_id int 消息id
    */
    public function readMessage($message_id)
    {
        return $this->where(array('id' => $message_id))->setField('is_read', 1);
    }
    /*设置某个用户的所有消息为已读*/
    public function setAllReaded($uid, $message_session = '')
    {
        if ($message_session != '') {
            $map['type'] = $message_session;
        }
        $map['to_uid'] = $uid;
        $map['is_read'] = 0;
        $this->where($map)->setField('is_read', 1);
        return true;
    }

    /**获取全部没有提示过的消息
     * @param $uid 用户ID
     * @return mixed
     */
    public function getHaventToastMessage($uid)
    {
        $messages = D('message')->where('to_uid=' . $uid . ' and  is_read=0  and last_toast=0')->order('id desc')->limit(99999)->select();
        $this->_initMessage($messages);
        return $messages;
    }

    /**设置全部未提醒过的消息为已提醒
     * @param $uid
     */
    public function setAllToasted($uid)
    {
        $now = time();
        D('message')->where('to_uid=' . $uid . ' and last_toast=0')->setField('last_toast', $now);
    }

    /**
     * 取回某类型全部未读信息
     * @param $map
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function getHaventReadMeassage($map)
    {
        $map['is_read'] = 0;
        $messages = $this->where($map)->order('id desc')->limit(99999)->select();
        $this->_initMessage($messages);
        return $messages;
    }

    /**
     * 获取全部会话类型及模板
     * @return array|void
     * @author 大蒙<59262424@qq.com>
     */
    public function getAllMessageType()
    {   
        //echo APP_PATH;exit;
        $tag = 'ALL_MESSAGE_SESSION';
        //$message_session = S($tag);
        if (empty($message_session)) {

            $message_session = load_config(CONF_PATH . 'message_config.php');

            $message_session = $message_session['session'];
            foreach ($message_session as &$val) {
                if ($val['name'] == '') {
                    $val['name'] = 'Common';
                } else {
                    $val['name'] = 'Common_' . $val['name'];
                }
                $val['module'] = 'Common';
                $val['icon'] = '/Public/images/message_icon/' . $val['icon'];
                !isset($val['sort']) && $val['sort'] = 0;
                $val['alias']='系统';
            }
            unset($val);
            
            $model_message_session = array();
            $module_alias=array();
            $modules = D('Common/Module')->getAll(1);
            foreach ($modules as $val) {
                $path = APP_PATH . $val['name'] . '/Conf/message_config.php';
                if (file_exists($path)) {
                    $conf = load_config($path);
                    $conf = $conf['session'];
                }
                if (!in_array($val['name'], array('Core'))) {
                    //模块默认会话类型，logo使用模块logo，现在先用null代替,在下面替换为模块logo
                    $addArray = array(array('name' => '', 'title' => $val['alias'] . '消息', 'icon' => NULL));
                    if (count($conf)) {
                        $conf = array_merge($conf, $addArray);
                    } else {
                        $conf = $addArray;
                    }
                }
                $model_message_session[$val['name']] = $conf;
                $conf = null;
                $module_alias[$val['name']]=$val['alias'];
            }
            unset($val);

            foreach ($model_message_session as $key => $val) {
                $has = 0;
                foreach ($val as $one_type) {
                    if ($one_type['name'] == '') {
                        if ($has == 0) {
                            $has = 1;
                            $one_type['name'] = $key;
                        } else {
                            continue;
                        }
                    } else {
                        $one_type['name'] = $key . '_' . $one_type['name'];
                    }
                    $one_type['module'] = $key;
                    !isset($one_type['sort']) && $one_type['sort'] = 0;
                    if($one_type['icon']==null){

                        $one_type['icon'] = APP_PATH . $key . '/Static/images/message_icon.png';//使用模块logo
                        //图标文件不存在用系统默认图标
                        if(!file_exists($one_type['icon'])){
                            $one_type['icon'] = '/Public/images/message_icon/system.png';//模块logo不存在使用系统logo
                        }else{
                            $one_type['icon'] = '/Application/' . $key . '/Static/images/message_icon.png';//使用模块logo
                        }
                    }
                    $one_type['alias']=$module_alias[$key];
                    $message_session[] = $one_type;
                }
            }
            unset($key, $val, $one_type);
            $message_session = array_combine(array_column($message_session, 'name'), $message_session);
            //S($tag, $message_session);
        }

        return $message_session;
    }

    /**
     * 获取某人的会话类型
     * @param int $uid
     * @return mixed
     */
    public function getMyMessageType($uid = 0)
    {
        !$uid && $uid = is_login();
        if (!$uid) {
            $this->error = '请先登录！';
        }
        $tag = 'MY_MESSAGE_SESSION_' . $uid;
        $message_type = S($tag);
        if ($message_type === false) {
            $map['uid'] = $uid;
            $map['status'] = 1;
            $message_type = M('MessageType')->where($map)->select();
            S($tag, $message_type);
        }
        return $message_type;
    }

    /**
     * 获取用户会话类型列表，带详情和消息数统计及排序的
     * @param int $uid
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getMyMessageTypeList($uid = 0)
    {
        !$uid && $uid = is_login();
        if (!$uid) {
            $this->error = '请先登录！';
        }
        $my_types = $this->getMyMessageType($uid);
        $type_list = $this->getAllMessageType();
        foreach ($my_types as $key => &$val) {
            if (!$type_list[$val['type']]) {
                unset($my_types[$key]);
                continue;
            }
            $val['detail'] = $type_list[$val['type']];
            $map['to_uid'] = $uid;
            $map['type'] = $val['type'];
            $map['is_read'] = 0;
            $val['count'] = $this->where($map)->count();
            
            $map_last['to_uid'] = $uid;
            $map_last['type'] = $val['type'];
            $lastMessage = $this->where($map_last)->order('id desc')->find();
            if ($lastMessage) {
                $val['last_message'] = $this->getContent($lastMessage['content_id']);
            }
            if ($val['count'] > 0) {
                $val['sort'] = $val['detail']['sort'] + 1000;
            } else {
                $val['sort'] = $val['detail']['sort'];
            }
        }
        unset($val);
        return list_sort_by($my_types, 'sort', 'desc');
    }

    /**
     * 根据类型标识获取类型详细信息
     * @param $type
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getInfo($type)
    {
        $allType = $this->getAllMessageType();
        return $allType[$type];
    }
    private function _initMessage(&$messages)
    {
        foreach ($messages as &$v) {
            if (in_array($v['type'], array('1', '2','3','0','4', '5'))) {
                $v['type'] = 'Common_system';
            }
            $v['ctime'] = friendlyDate($v['create_time']);
            $v['content'] = $this->getContent($v['content_id']);
            if(is_array($v['content']['content'])){
                $v['content']['untoastr']=1;
            }
        }
        unset($v);
        return true;
    }

    //////////////////////////////////////////muucmf/////////////////////////////////////


}
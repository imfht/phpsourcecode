<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;

/**
 *  消息控制器
 */
class Message extends Admin
{


    public function userList($r=20)
    {
        $aSearch1 = input('get.user_search1','');
        $aSearch2 = input('get.user_search2',0,'intval');
        $user_order = input('get.user_order',0,'intval');
        if($user_order==0){
            $order = 'uid desc';
        }
        if($user_order==1){
            $order = 'last_login_time desc';
        }
        if($user_order==2){
            $order = 'login desc';
        }
        $map['status'] = 1;;
        if (empty($aSearch1) && empty($aSearch2)) {
            $aUserGroup = input('get.user_group', 0, 'intval');
            $aRole = input('get.role', 0, 'intval');

            if (!empty($aRole) || !empty($aUserGroup)) {
                $uids = $this->getUids($aUserGroup, $aRole);
                $map['uid'] = array('in', $uids);
            }
        } else {
            $uids = $this->getUids_sc($aSearch1, $aSearch2);
            $map['uid'] = array('in', $uids);
        }
        $user = Db::name('member')->where($map)->order($order)->field('uid,nickname,login,last_login_time,last_login_ip')->paginate(20);
        // 获取分页显示
        $page = $user->render();
        // 转为数组
        $user = $user->toArray()['data'];

        foreach ($user as &$v) {
            $v['id'] = $v['uid'];
            $v['last_login_ip'] = long2ip($v['last_login_ip']);
            $user_info = query_user(['mobile','email'],$v['uid']);
            $v['mobile'] = $user_info['mobile'];
            $v['email'] = $user_info['email'];
        }
        unset($v);

        $role = model('role')->selectByMap(array('status' => 1));
        $user_role = array(array('id' => 0, 'value' => lang('_ALL_')));
        foreach ($role as $key => $v) {
            array_push($user_role, array('id' => $v['id'], 'value' => $v['title']));
        }

        $group = model('AuthGroup')->getGroups();

        $user_group = array(array('id' => 0, 'value' => lang('_ALL_')));
        foreach ($group as $key => $v) {
            array_push($user_group, array('id' => $v['id'], 'value' => $v['title']));
        }

        $order_array = array(array('id'=>'0','value'=>lang('_DEFAULT_')),array('id'=>'1','value'=>lang('_LAST_LOGIN_TIME_')),array('id'=>'2','value'=>lang('_LOGIN_COUNT_')));

        $builder = new AdminListBuilder();
        $builder->title(lang('_"MASS_USER_LIST"_'));

        $builder
            ->setSelectPostUrl(Url('Message/userList'))
            ->setSearchPostUrl(Url('Message/userList'))
            ->select('排序：','user_order','select','排序','','',$order_array)
            ->select(lang('_USER_GROUP:_'), 'user_group', 'select', lang('_FILTER_ACCORDING_TO_USER_GROUP_'), '', '', $user_group)
            ->select(lang('_IDENTITY_'), 'role', 'select', lang('_FILTER_ACCORDING_TO_USER_IDENTITY_'), '', '', $user_role)
            ->search('','user_search1','',lang('_SEARCH_ACCORDING_TO_USERS_NICKNAME_'),'','','')
            ->search('','user_search2','',lang('_SEARCH_ACCORDING_TO_USER_ID_'),'','','');
        $builder->buttonModalPopup(Url('Message/sendMessage'), array('user_group' => $aUserGroup, 'role' => $aRole), lang('_SEND_A_MESSAGE_'), array('data-title' => lang('_MASS_MESSAGE_'), 'target-form' => 'ids', 'can_null' => 'true'));

        //$builder->buttonModalPopup(Url('Message/sendMobileMessage'), array('user_group' => $aUserGroup),lang('_SNS_SEND_'), array('data-title' => lang('_SNS_SEND_'), 'target-form' => 'ids', 'can_null' => 'true'));

        $builder->keyText('uid', lang('_USER_ID_'))
                ->keyText('nickname', lang('_"NICKNAME"_'))
                ->keyText('mobile',lang('_CELL_PHONE_NUMBER_'))
                ->keyText('login', lang('_LOGIN_COUNT_'))
                ->keyTime('last_login_time', lang('_LAST_LOGIN_TIME_'))
                ->keyText('last_login_ip', lang('_LOGIN_IP_LAST_TIME_'));

        $builder->data($user);
        $builder->page($page);
        $builder->display();


    }

    private function getUids($user_group = 0, $role = 0)
    {
        $uids = array();
        if (!empty($user_group)) {
            $users = Db::name('auth_group_access')->where(array('group_id' => $user_group))->field('uid')->select();
            $group_uids = getSubByKey($users, 'uid');
            if ($group_uids) {
                $uids = $group_uids;
            }
        }
        if (!empty($role)) {
            $users = Db::name('user_role')->where(array('role_id' => $role))->field('uid')->select();
            $role_uids = getSubByKey($users, 'uid');
            if ($role_uids) {
                $uids = $role_uids;
            }
        }
        if (!empty($role) && !empty($user_group)) {
            $uids = array_intersect($group_uids, $role_uids);
        }
        return $uids;


    }
    private function getUids_sc($search_nn = "", $search_id = 0)
    {
        $uids = array();
        if (!empty($search_nn)) {
            $users = Db::name('member')->where(array('nickname' => $search_nn))->field('uid')->select();
            $uids_nn = getSubByKey($users, 'uid');
            if ($uids_nn) {
                $uids = $uids_nn;
            }
        }
        if (!empty($search_id)) {
            $users = Db::name('member')->where(array('uid' => $search_id))->field('uid')->select();
            $uids_id = getSubByKey($users, 'uid');
            if ($uids_id) {
                $uids = $uids_id;
            }
        }
        if (!empty($search_id) && !empty($search_nn)) {
            $uids = array_intersect($search_id, $search_nn);
        }
        return $uids;
    }

    public function sendMessage()
    {

        if (request()->isPost()) {
            $aSendType=input('post.sendType/a',array());
            $aUids = input('post.uids');
            $aUserGroup = input('post.user_group/a');
            $aUserRole = input('post.user_role/a');
            $aTitle = input('post.title', '', 'text');
            $aContent = input('post.content', '', 'html');
            $aUrl = input('post.url', '', 'text');
            $aArgs = input('post.args/a', '', 'text');
            $args = [];
            // 转换成数组
            if ($aArgs) {
                $array = explode('/', $aArgs);
                while (count($array) > 0) {
                    $args[array_shift($array)] = array_shift($array);
                }
            }

            if (empty($aTitle)) {
                $this->error(lang('_PLEASE_ENTER_THE_MESSAGE_HEADER_'));
            }
            if (empty($aContent)) {
                $this->error(lang('_PLEASE_ENTER_THE_MESSAGE_CONTENT_'));
            }
            // 以权限组或身份发送消息
            if(empty($aUids)){
                if (empty($aUserGroup) && empty($aUserRole)) {
                    $this->error(lang('_PLEASE_SELECT_A_USER_GROUP_OR_AN_IDENTITY_GROUP_OR_USER_'));
                }

                $role_count = Db::name('Role')->where(array('status' => 1))->count();
                $group_count = Db::name('AuthGroup')->where(array('status' => 1))->count();
                if ($role_count == count($aUserRole)) {
                    $aUserRole = 0;
                }
                if ($group_count == count($aUserGroup)) {
                    $aUserGroup = 0;
                }
                if (!empty($aUserRole)) {
                    $uids = Db::name('user_role')->where(['role_id' => ['in', $aUserRole]])->field('uid')->select();
                }
                if (!empty($aUserGroup)) {
                    $uids = Db::name('auth_group_access')->where(['group_id' => ['in', $aUserGroup]])->field('uid')->select();
                }
                if (empty($aUserRole) && empty($aUserGroup)) {
                    $uids = Db::name('Member')->where(['status' => 1])->field('uid')->select();
                }
                $to_uids = getSubByKey($uids, 'uid');
            }else{
                // 用uid发送消息
                $to_uids = explode(',',$aUids);
            }

            if(in_array('systemMessage',$aSendType)){
                $resMessage=model('Message')->sendMessageWithoutCheckSelf($to_uids, $aTitle, $aContent, $aUrl, $args);
                if($resMessage!==true){
                    $this->error('发送失败');
                }
            }
            if(in_array('systemEmail',$aSendType)){
                $resEmail=model('Message')->sendEmail($to_uids, $aTitle, $aContent, $aUrl, $args);
                if($resEmail!==true){
                    $this->error($resEmail);
                }
            }
            if(in_array('mobileMessage',$aSendType)){
                $resMobile=model('Message')->sendMobileMessage($to_uids, $aTitle, $aContent, $aUrl, $args);
                if($resMobile!==true){
                    $this->error($resMobile);
                }
            }
            $result['status'] = 1;
            $result['info'] = lang('_SEND_');
            return json($result);
        } else {
            $aUids = input('get.ids/a');
            $aUserGroup = input('get.user_group', 0, 'intval');
            $aRole = input('get.role', 0, 'intval');
            if (empty($aUids)) {
                $role = model('role')->selectByMap(array('status' => 1));
                $roles = array();
                foreach ($role as $key => $v) {
                    array_push($roles, array('id' => $v['id'], 'value' => $v['title']));
                }
                $group = model('AuthGroup')->getGroups();
                $groups = array();
                foreach ($group as $key => $v) {
                    array_push($groups, array('id' => $v['id'], 'value' => $v['title']));
                }
                $this->assign('groups', $groups);
                $this->assign('roles', $roles);
                $this->assign('aUserGroup', $aUserGroup);
                $this->assign('aRole', $aRole);
            } else {
                $uids = implode(',',$aUids);
                $users = model('Member')->where(['uid'=>['in',$aUids]])->field('uid,nickname')->select();
                $this->assign('users', $users);
                $this->assign('uids', $uids);
            }
            return $this->fetch('sendmessage');
        }
    }
    public function sendMobileMessage(){
        if (request()->isPost()) {
            $aUids = input('post.uids');
            $aUserGroup = input('post.user_group');
            $aContent = input('post.content', '', 'html');

            if (empty($aContent)) {
                $this->error(lang('_PLEASE_ENTER_THE_MESSAGE_CONTENT_'));
            }
            // 以权限组或身份发送消息
            if(empty($aUids)){
                if (empty($aUserGroup)) {
                    $this->error(lang('_PLEASE_SELECT_A_USER_GROUP_OR_AN_IDENTITY_GROUP_OR_USER_'));
                }

                $group_count = Db::name('AuthGroup')->where(['status' => 1])->count();
                
                if (!empty($aUserGroup)) {
                    $uids = Db::name('auth_group_access')->where(['group_id' => ['in', $aUserGrou]])->field('uid')->select();
                }
                $to_uids = getSubByKey($uids, 'uid');
            }else{
                // 用uid发送消息
                $to_uids = explode(',',$aUids);
            }
            $user_moblie = Db::name('UcenterMember')->where(['id'=>['in',$to_uids]])->field('id,mobile')->select();

            //开始循环发送短信
            $success_num = 0; //初始个发送成功数

            foreach ($user_moblie as $v) {
                if($v['mobile']){
                    $res = sendSMS($v['mobile'], $aContent);
                    if ($res === true) {
                        //下一版本可增加入库来统计和跟踪发送效果
                        $success_num = $success_num+1;
                    }
                }
            }
            //返回成功信息
            $result['code'] = 1;
            $result['msg'] = lang('_SEND_').','.$success_num.'条';
            return json($result);
        } else {
            $aUids = input('get.ids');
            $aUserGroup = input('get.user_group', 0, 'intval');
            $aRole = input('get.role', 0, 'intval');
            if (empty($aUids)) {
                $role = model('admin/Role')->selectByMap(array('status' => 1));
                $roles = array();
                foreach ($role as $key => $v) {
                    array_push($roles, array('id' => $v['id'], 'value' => $v['title']));
                }
                $group = model('AuthGroup')->getGroups();
                $groups = array();
                foreach ($group as $key => $v) {
                    array_push($groups, array('id' => $v['id'], 'value' => $v['title']));
                }
                $this->assign('groups', $groups);
                $this->assign('aUserGroup', $aUserGroup);
            } else {
                $uids = implode(',',$aUids);
                $users = D('Member')->where(array('uid'=>array('in',$aUids)))->field('uid,nickname')->select();
                $this->assign('users', $users);
                $this->assign('uids', $uids);
            }
            return $this->fetch('sendmobilemessage');
        }
    }



    /**
    *消息设置
     **/
    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $admin_config
            ->title("消息会话配置")
            ->data($data)
            ->keySelect('MESSAGE_TYPE_TPL', '会话列表模板', '', array('type1' => '官方模板1','type2' => '官方模板2','type3' => '官方模板3','type4' => '官方模板4'))
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }

    /**
     * 系统会话列表
     */
    public function messageTypeList()
    {
        $message_sessions=get_all_message_type();

        foreach($message_sessions as &$val){

            if(isset($val['tpl_name'])){
                $val['tpl_name']=APP_PATH.$val['module'].'/.../'.$val['tpl_name'].'.html';
            }else{
                $val['tpl_name']=APP_PATH.'Common/.../_message_li.html';
            }
        }
        unset($val);
        //dump($message_sessions);exit;
        $builder=new AdminListBuilder();
        $builder
            ->title('消息类型列表')
            ->suggest('这里只能查看和刷新，要对会话做增删改，请修改对应文件')
            ->buttonAjax(url('message/sessionRefresh'),null,'刷新',['hide-data' => 'true'])
            ->keyText('name','标识（发送消息时的$type参数值）')
            ->keyTitle('消息列表')
            ->keyText('alias','所属模块')
            ->keyImage('icon','消息图标（自定义消息icon.png）')
            ->keyText('sort','排序值')
            ->keyText('tpl_name','消息模板(“...”表示“View/Message/”)')
            ->data($message_sessions)
            ->display();
    }
    public function sessionRefresh()
    {
        cache('ALL_MESSAGE_SESSION',null);
        $this->success('刷新成功！',Url('Message/messageTypeList'));
    }
    private function _toShowArray(&$data)
    {
        if(is_array($data)){
            $str="\$messageContent=array(<br>";
            foreach($data as $key=>$val){
                $str.="&nbsp;&nbsp;&nbsp;&nbsp;'".$key."'=>'".$val."',<br>";
            }
            unset($key,$val);
            $str.=');';
            return $str;
        }
        return $data;
    }


}

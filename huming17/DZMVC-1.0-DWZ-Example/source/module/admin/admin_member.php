<?php

switch ($do) {
    case "update":
        $update_user_info = array();
        //DEBUG 是否更新用户名
        $realname = gpc('realname', 'GP', $_G['username']);
        if (!empty($realname)) {
            $update_user_info['user_realname'] = $realname;
        }
        //DEBUG 是否更新密码
        $password = gpc('password', 'GP', $_G['password']);
        if (!empty($password)) {
            $update_user_info['user_password'] = encode_password($password);
        }
        if (!empty($update_user_info)) {
            $update_user_info['user_modify_time'] = $_G['timestamp'];
            DB::update('users', $update_user_info, 'user_id=' . $_G['user_id']);
            echo '{
                "statusCode":"200",
                "message":"'.lang('core','operation_successful').'",
                "navTabId":"",
                "rel":"",
                "callbackType":"",
                "forwardUrl":"",
                "confirmMsg":""
            }';
        }
        break;
    default:
        include template('admin/member/memberinfo');
}
?>
<?php
/**是否已经加入
 * @auth 陈一枭
 */
function is_joined($group_id)
{
    $check = D('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login(),'status'=>1))->find();
    return $check?true:false;
}

function getGroupName($group_id){
  $group =  D('Group')->where(array('id'=>$group_id))->find();
   return $group['title'];
}


 function getGroupCate($group_id){
    $group =  D('Group')->where(array('id'=>$group_id,'status'=>1))->find();
    return getGroupCateByTypeId($group['type_id']);
}
 function getGroupCateByTypeId($type_id){
    $type =  D('GroupType')->where(array('id'=>$type_id,'status'=>1))->find();
    return $type['title'];
}


function checkIsCreator($uid,$table,$row_id){
    $row = D($table)->where(array('id'=>$row_id))->find();
    if($row['uid'] == $uid){
        return true;
    }else{
        return false;
    }
}

function getPostCateName($type_id){
    $type =  D('GroupPostCategory')->where(array('id'=>$type_id,'status'=>1))->find();
    return $type['title'];
}

function getMemberStatus($group_id,$uid){

    $check = D('GroupMember')->where(array('group_id' => $group_id, 'uid' => $uid))->find();

    return $check;
}


function get_user_action($app='',$mod='',$action='',$uid=0){
    $uid == 0 && $uid = is_login();
    $user_action = D('UserAction')->where(array('app'=>$app,'mod'=>$mod,'action'=>$action,'uid'=>$uid))->find();
    if($user_action) return true;
    return false;
}

 function isAllowEditGroup($group_id)
{
    if (!isGroupExists($group_id)) {
        return false;
    }
    if (!is_login()) {
        return false;
    }
    if (is_administrator()) {
        return true;
    }

    $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
    if ($group['uid'] != is_login()) {
        return false;
    }
    return true;
}

 function isGroupExists($group_id)
{
    $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
    return $group ? true : false;
}
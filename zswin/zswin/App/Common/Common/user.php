<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zswin.cn
// +----------------------------------------------------------------------
function checkscore($uid){
	$score=M('member')->where(array('uid'=>$uid))->getField('score');
	
	if($score==null||!$score){
		return false;
	}
	
	$map['score']=array('elt',$score);
	$map['status']=1;
	$mrole=M('mrole')->where($map)->order('score desc')->find();
	
	
	$RoleUser = M("MroleUser");
	$Role['user_id'] = $uid;
	$roleuser=$RoleUser->where($Role)->find();
	
	if($roleuser==null){
		$Role['role_id'] = $mrole['id'];
		$RoleUser->add($Role);
	}else if($roleuser['role_id']!=$mrole['id']){
		
		$RoleUser->where($Role)->setField('role_id',$mrole['id']);
	}else{
		
		
	}
	
	
	
	return true;
	
	
	
}

function setuserscore($uid,$score,$inc=true){
	
	$member=D('Member');
	
	if($inc){
		
		$res=$member->where(array('uid'=>$uid))->setInc('score',$score);
		clean_query_user_cache($uid,array('artnum'));
	}else{
		$res=$member->where(array('uid'=>$uid))->setDec('score',$score);
		clean_query_user_cache($uid,array('artnum'));
	}
	
	return $res;
}



/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}
function is_admin($uid = null)
{
	$model=D('role_user');
	$admin_uids=$model->getField('user_id',true);
    $uid = is_null($uid) ? is_login() : $uid;
   
   
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}
function getmroleidByuid($uid) {
	
	$mroleUser = M("MroleUser");
	$map['user_id']=$uid;
	
	$roleIdList = $mroleUser->where($map)->find();
	$roleId = $roleIdList['role_id'];
	
	return $roleId;
	
	
}
function getMroleNameByUserId($id) {
	$mroleUser = M("MroleUser");
	$roleIdList = $mroleUser->where("user_id=$id")->find();
	$roleId = $roleIdList['role_id'];
	if ($roleId == 0) {
		return '无会员组';
	}

	$dao = D("Mrole");
	$list = $dao->select(array('field' => 'id,name'));
	foreach ($list as $vo) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$roleId];
	return $name;
}
function getarteditauth($id,$uid){
	//获得是否有编辑文章的权利
	$uid = $uid ? $uid : is_login();
	
	if($uid==1){
		return true;
	}
	$map['id']=$id;
	$info=D('Article')->where($map)->find();
	
	
        if($info['uid'] == $uid){
        	if($info['status']==5||$info['status']==2){
		return true;
        		
	}else{
		
		
		$auth=getmroleauth($uid);
		if($auth['yesartedit']){
			
			
         if( $info['create_time'] + $auth['artedittime']*60<time()){
    	    return false;
         }else{
    	    return true;
         }
			
			
			
		}else{
			return true;
		}
	
		
	}
		}else{
			return false;
		}
	
	

	
	
	
}
function get_at_usernames($content)
{
	
    //正则表达式匹配
  $user_pattern = "/\\@([^\\#|\\s|\,|^\\<]+)/";
    preg_match_all($user_pattern, $content, $users);

    //返回用户名列表
    return array_unique($users[1]);
}

function get_at_uids($content)
{
    $usernames = get_at_usernames($content);
    $result = array();
    foreach ($usernames as $username) {
        $user = D('Member')->where(array('nickname' => op_t($username)))->field('uid')->find();
        $result[] = $user['uid'];
    }
    
    
    return $result;
}
function getmroleauth($uid = null)
{
    //默认获取自己的资料
    $uid = $uid ? $uid : is_login();
    if (!$uid) {
        return null;
    }
    $roleId=getmroleidByuid($uid);
    
    
    $val=S('MROLE_CONFIG_DATA'.$roleId);
    if(!isset($val)){
    	
    	$roleauth=S('MROLE_CONFIG_DATA'.$roleId);
    	
    }else{
    $map['id']=$roleId;
    $info=D('Mroleconfig')->where($map)->find();
    
    $info['value']=json_decode($info['value'],true);
    S('MROLE_CONFIG_DATA'.$roleId,$info['value']);	
    $roleauth=$info['value'];
    	
    }
   
	
        	
      return $roleauth; 	

	
	
	
	
}
/**
 * 支持的字段有
 * member表中的所有字段，ucenter_member表中的所有字段
 * 等级：title
 * 头像：avatar32 avatar64 avatar128 avatar256 avatar512
 * 个人中心地址：space_url
 * 认证图标：icons_html
 *
 * @param      $fields array|string 如果是数组，则返回数组。如果不是数组，则返回对应的值
 * @param null $uid
 * @return array|null
 */
function query_user($fields, $uid = null)
{
    //如果fields不是数组，则返回值也不是数组
    if (!is_array($fields)) {
        $result = query_user(array($fields), $uid);
        return $result[$fields];
    }

    //默认获取自己的资料
    $uid = $uid ? $uid : is_login();
    if (!$uid) {
        return null;
    }



    //获取两张用户表格中的所有字段
    $homeModel = M('Member');
    $ucenterModel = M('UcenterMember');
    $homeFields = $homeModel->getDbFields();
    $ucenterFields = $ucenterModel->getDbFields();

    //分析每个表格分别要读取哪些字段
    $avatarFields = array('avatar32', 'avatar64', 'avatar128', 'avatar256', 'avatar512');
    $avatarFields = array_intersect($avatarFields, $fields);
    $homeFields = array_intersect($homeFields, $fields);
    $ucenterFields = array_intersect($ucenterFields, $fields);

    //查询需要的字段
    $homeResult = array();
    $ucenterResult = array();
    
   
    if ($homeFields) {
        $homeResult = D('Home/Member')->where(array('uid' => $uid))->field($homeFields)->find();
    }
    if ($ucenterFields) {
        $model = D('User/UcenterMember');
        $ucenterResult = $model->where(array('id' => $uid))->field($ucenterFields)->find();
    }


    //读取头像数据
    $result = array();
    $avatarAddon = new \Addons\Avatar\AvatarAddon();
    foreach ($avatarFields as $e) {
        $avatarSize = intval(substr($e, 6));
        $avatarUrl = $avatarAddon->getAvatarPath($uid, $avatarSize);

        $result[$e] = $avatarUrl;
    }

    //读取等级数据
    if (in_array('title', $fields)) {
        $titleModel = D('Usercenter/Title');
        $title = $titleModel->getTitle($uid);
        $result['title'] = $title;
    }

    if(in_array('supportnum', $fields)){
    	$result['supportnum'];
    }
    if(in_array('commentnum', $fields)){
    	
    	
    	
    	
    	$result['commentnum'];
    }
    if(in_array('allartnum', $fields)){
    	
    	
    	$artnum=M('Article')->where(array('uid' => $uid))->count();
    	$result['allartnum']=empty($artnum)?0:$artnum;
    }
    if(in_array('artnum', $fields)){
    	
    	
    	$artnum=M('Article')->where(array('uid' => $uid,'status'=>1))->count();
    	$result['artnum']=empty($artnum)?0:$artnum;
    }
    if(in_array('focusnum', $fields)){
    	$mapfocusnum['id']=$uid;
    	$mapfocusnum['type']=0;
    	 
    	 
    	 
    	$result['focusnum'] = D('Focus')->where($mapfocusnum)->count();
    	
    }
    if(in_array('scartnum', $fields)){
    	 
    	$mapscartnum['id']=$uid;
    	$mapscartnum['type']=1;
    	 
    	 
    	 
    	$result['scartnum'] = D('Focus')->where($mapscartnum)->count();  
      }
    if(in_array('tagfocusnum', $fields)){
    	$maptagfocusnum['id']=$uid;
    	$maptagfocusnum['type']=2;
    	
    	
    	
    	$result['tagfocusnum'] = D('Focus')->where($maptagfocusnum)->count();
    	 
    	
    }
   
    if(in_array('zan', $fields)){
    	 
    	 
    	$zannum=M('Article')->where(array('uid' => $uid))->sum('ding');
    	$result['zan']=empty($zannum)?0:$zannum;
    }
    
    //读取用户名拼音
    if (in_array('pinyin', $fields)) {

        $result['pinyin'] = D('Pinyin')->pinYin($result['nickname']);
    }

    //获取个人中心地址
    $spaceUrlResult = array();
    if (in_array('space_url', $fields)) {
        $result['space_url'] = ZSU('/userart/'.$uid,'Ucenter/userart',array('uid'=>$uid));
    }

   $ucenterResult['nickname']=op_t($ucenterResult['nickname']);
    //获取昵称链接
    if (in_array('space_link', $fields)) {
        $result['space_link'] = '<a ucard="' . $uid . '" href="' . ZSU('/userart/'.$uid,'Ucenter/userart',array('uid'=>$uid)) . '">' . $ucenterResult['nickname'] . '</a>';
    }

    //获取用户头衔链接
    if (in_array('rank_link', $fields)) {
        $rank_List = D('rank_user')->where('uid=' . $uid)->select();
        $num = 0;
        foreach ($rank_List as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = fixAttachUrl(D('picture')->where('id=' . $rank['logo'])->getField('path'));
            if ($val['is_show']) {
                $num = 1;
            }
        }
        if ($rank_List) {
            $rank_List[0]['num'] = $num;
            $result['rank_link'] = $rank_List;
        } else {
            $result['rank_link'] = array();
        }

    }
     if (in_array('signature', $fields)) {
     	$result['signature']=D('Home/Member')->where(array('uid' => $uid))->field($homeFields)->getField('signature');
     	if($result['signature']==null){
     		
     		$result['signature']='暂无个人签名';
     	}
     }
    //获取用户认证图标
    if (in_array('icons_html', $fields)) {

        //判断是否有手机图标
        $static = C('TMPL_PARSE_STRING.__STATIC__');
        $iconUrls = array();
        $user = query_user(array('mobile'), $uid);
        if ($user['mobile']) {
            $iconUrls[] = "$static/images/mobile-bind.png";
        }
        //生成结果
        $result['icons_html'] = '<span class="usercenter-verify-icon-list">';
        foreach ($iconUrls as $e) {
            $result['icons_html'] .= "<img src=\"{$e}\" title=\"对方已绑定手机\"/>";
        }
        $result['icons_html'] .= '</span>';
    }


    //粉丝数、关注数
    if (in_array('fensi', $fields)) {
    	
    	$mapfensi['rowid']=$uid;
    	$mapfensi['type']=0;
    	
    	
    	
        $result['fensi'] = D('Focus')->where($mapfensi)->count();
    }
   
    

    //↑↑↑ 新增字段应该写在在这行注释以上 ↑↑↑

    //合并结果，不包括缓存
    $result = array_merge($ucenterResult, $homeResult, $spaceUrlResult, $result);

 

    //返回结果
    return $result;
}

function read_query_user_cache($uid, $field)
{
    return S("query_user_{$uid}_{$field}");
}

function write_query_user_cache($uid, $field, $value)
{
    return S("query_user_{$uid}_{$field}", $value, 1800);
}

function clean_query_user_cache($uid, $field)
{
    if (is_array($field)) {
        foreach ($field as $field_item) {
            S("query_user_{$uid}_{$field_item}", null);
        }
    }
    S("query_user_{$uid}_{$field}", null);
}
/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return $_SESSION['zs_home']['user_auth']['username'];
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if ($info && isset($info[1])) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}
function getnowUid(){
	
	if($_SESSION['zs_home']['user_auth']['uid']>0){
		return $_SESSION['zs_home']['user_auth']['uid'];
	}else{
		return false;
	}
	
	
}
/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname']) {
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}
function hasguanzhu($rowid,$uid,$type){
	//用于判断后面的uid代表的用户是否关注了前者(前者可以表示用户，标签和文章，分类)
	
	$map['id']=$uid;
	$map['rowid']=$rowid;
	$map['type']=$type;
	if(M('Focus')->where($map)->count()>0){
		return true;
	}else{
		return false;
	}
}
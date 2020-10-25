<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

function addWeixinLog($data, $data_post = '',$mp_id = 0, $mid = 0,$ToUserName = '',$FromUserName = '',$type = '') {
    $log ['cTime'] = time ();
    $log ['cTime_format'] = date ( 'Y-m-d H:i:s', $log ['cTime'] );
    $log ['data'] = is_array ( $data ) ? serialize ( $data ) : $data;
    $log ['mp_id'] = $mp_id;
    $log ['mid'] = $mid;
    $log ['ToUserName'] = $ToUserName;
    $log ['FromUserName'] = $FromUserName;
    $log ['type'] = $type;
    $log ['data_post'] = is_array ( $data_post ) ? serialize ( $data_post ) : $data_post;
    db ( 'weixin_log' )->insert ( $log );
}

// 获取当前用户的mp_Token
function get_token($mp_token = NULL) {
    if ($mp_token !== NULL) {
        session ( 'mp_token', $mp_token );
    } elseif (! empty ( $_REQUEST ['mp_token'] )) {
        session ( 'mp_token', $_REQUEST ['mp_token'] );
    }
    $mp_token = session ( 'mp_token' );
    if (empty ( $mp_token )) {
        $mp_token = session('user_auth.mp_token');
    }
    if (empty ( $mp_token )) {
        return - 1;
    }

    return $mp_token;
}

// 获取公众号的信息
function get_token_appinfo($token = '') {
    empty ( $token ) && $token = get_token ();
    $map ['public_id'] = $token;
    $info = model ( 'member_public' )->where ( $map )->find ();
    return $info;
}

function get_mpid_appinfo($mp_id = '') {
    empty ( $mp_id ) && $mp_id = get_mpid ();
    $map ['mp_id'] = $mp_id;
    $info = model ( 'member_public' )->where ( $map )->find ();
    return $info;
}
// 获取公众号的信息
function get_token_appname($token = '') {
    empty ( $token ) && $token = get_token ();
    $map ['public_id'] = $token;
    $info = model ( 'member_public' )->where ( $map )->find ();
    return $info['public_name'];
}

// 判断是否是在微信浏览器里
function isWeixinBrowser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}

// php获取当前访问的完整url地址
function GetCurUrl() {
    $url = 'http://';
    if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos ( $url, '?' ) === false) {
        $url .= '?t=' . time ();
    }
    return $url;
}
// 获取当前用户的OpenId
function get_openid($openid = NULL) {
    $mp_id = get_mpid ();
    if ($openid !== NULL) {
        session ( 'openid_' . $mp_id, $openid );
    } elseif (! empty ( $_REQUEST ['openid'] )) {
        session ( 'openid_' . $mp_id, $_REQUEST ['openid'] );
    }
    $openid = session ( 'openid_' . $mp_id );

    $isWeixinBrowser = isWeixinBrowser ();
    //下面这段应该逻辑没问题，如果公众号配置信息错误或者没有snsapi_base作用域的获取信息权限可能会出现死循环，注释掉以下if可治愈
    if ( empty($openid) && $isWeixinBrowser) {

       // $callback = GetCurUrl ();
       // OAuthWeixin ( $callback );
        $info = get_mpid_appinfo ();

        $options['token'] = APP_TOKEN;
        $options['appid'] = $info['appid'];    //初始化options信息
        $options['appsecret'] = $info['secret'];
        $options['encodingaeskey'] = $info['encodingaeskey'];
        $auth = new Com\Wxauth($options);

	    $openid =  $auth->open_id;
	    session ( 'openid_' . $mp_id, $openid );                   //openid 存进session
        session ( 'wxuser_' . $mp_id.$openid, $auth->wxuser );     //wxauth获得的微信用户信息存到session中

    }

    if (empty ( $openid )) {
        return 0;
    }

    return $openid;
}


// 设置当前上下文的公众号mp_id
function set_mpid($mp_id = NULL) {
    if ($mp_id !== NULL) {
        session ( 'mp_id', $mp_id );
    } elseif (! empty ( $_REQUEST ['mp_id'] )) {
        $mp_id = input('mp_id','','/^\w{32}$/');
        empty($mp_id) || session ( 'mp_id', $mp_id );
    }

    return $mp_id;
}
// 获取当前上下文的公众号mp_id
function get_mpid($mp_id = NULL) {

    if ($mp_id !== NULL) {
        session ( 'mp_id', $mp_id );
    } elseif (! empty ( input('mp_id') )) {
        $mp_id = input('mp_id');
        session ( 'mp_id', $mp_id );
    }
    $mp_id = session ( 'mp_id' );
    if (empty ( $mp_id )) {
        $mp_id = session('user_auth.mp_id');
    }
    if (empty ( $mp_id )) {
        $map['uid'] = is_login();
        $map['public_id'] = get_token();
        $mp =  model('MemberPublic')->where($map)->find();  //所登陆会员帐号当前管理的公众号
        $mp_id = $mp['mp_id'];
    }
    if (empty ( $mp_id )) {
        return - 1;
    }
    return $mp_id;
}


//根据mid获取粉丝用户信息
function get_mid_ucuser($mid = 0) {
  $model = model('Ucuser');
  $user = $model->find($mid);
  return $user;
}

//根据uid获取粉丝用户信息
function get_ucuser_byuid($uid) {
    if(empty($uid)){
        return false;
    }
    $model = model('Ucuser');
    $map['uid'] = $uid;
    $user = $model->where($map)->select();     //一个pc端帐号的uid可能对应多个公众号的mid
    return $user;
}


// 获取当前粉丝用户mid(ucuser表的mid),和 hook('init_ucuser',$params)作用基本相同。只在微信浏览器中可使用。
function get_ucuser_mid($mid = 0) {
    $mp_id = get_mpid ();
    if ($mid !== NULL) {
        session ( 'mid_' . $mp_id, $mid );
    } elseif (! empty ( input('mid') )) {
        session ( 'mid_' . $mp_id, input('mid') );
    }                                                                    //以上是带mid参数调用函数时设置session中的mid
    $mid = session ( 'mid_' . $mp_id );

    $isWeixinBrowser = isWeixinBrowser ();
    if(!$isWeixinBrowser){                           //非微信浏览器返回false，调用此函数必须对false结果进行判断，非微信浏览器不可访问调用的controller
        return false;
    }
    //下面这段应该逻辑没问题，如果公众号配置信息错误或者没有snsapi_base作用域的获取信息权限可能会出现死循环，注释掉以下if可治愈
    if ( $mid <= 0 && $isWeixinBrowser) {
        $map['openid'] = get_openid();
        $map['mp_id'] = $mp_id;
        $ucuser = model('Ucuser');
        $data = $ucuser->where($map)->find();
        if(!$data){                                                 //公众号没有这个粉丝信息，就注册一个
            $mid = $ucuser->registerUser( $map['mp_id'] ,$map['openid']);    //微信粉丝表ucuser表的mid
            session ( 'mid_' . $mp_id, $mid );

        }else{
            $mid =  $data['mid'];
            session ( 'mid_' . $mp_id, $mid );
        }
    }
    if (empty ( $mid )) {
        return - 1;
    }

    return $mid;
}

// 同步微信用户资料到本地存储。
function sync_wxuser($mp_id, $openid) {
    $model = model('Ucuser');
    $map['mp_id'] = $mp_id;
    $map['openid'] = $openid;
    $wxuser = session ( 'wxuser_' . $mp_id.$openid );
    if(!empty($wxuser['openid'])){         //通过oauth获取到过粉丝信息,在get_openid中获取和保存到了粉丝信息到session
        $user = $model->where($map)->find();
        if($user['status'] != 2){           //没有同步过粉丝信息
            $user = array_merge($user ,$wxuser);
            $user['status'] = 2;
            $model->save($user);
        }
        return $user;
    }

    return false;
}

//获取分享url的方法，解决controler在鉴权时二次回调jssdk获取分享url错误的问题
function get_shareurl(){
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $findme   = 'https://open.weixin.qq.com/';
    $pos = strpos($url, $findme);
    // 使用 !== 操作符。使用 != 不能像我们期待的那样工作，
    // 因为 'a' 的位置是 0。语句 (0 != false) 的结果是 false。
    $share_url = '';
    if ($pos !== false) {             //url是微信的回调授权地址
        return '';
    } else {                           //url是本地的分享地址
        return $url;
    }
}

//根据openid获取member用户信息
function get_member_by_openid($openid) {
    if(empty($openid)){
        return false;
    }
    $model = model('Ucuser');
    $map['openid'] = $openid;
    $user = $model->where($map)->find();
    if(empty($user['mid'])){           //粉丝没有关联的member帐号，在微信端和pc端都没有注册过
        return false;
    }
    $member = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature', 'score1'), $user['mid']);

    return $member;
}

//根据openid获取ucuser用户信息
function get_ucuser_by_openid($openid) {
    if(empty($openid)){
        return false;
    }
    $model = model('Ucuser');
    $map['openid'] = $openid;
    $user = $model->where($map)->find();

    return $user;
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function mpid_md5($str, $key = '')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}
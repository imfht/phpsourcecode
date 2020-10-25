<?php
namespace App\Controller;
use Core\Model\Account;
use Core\Model\Member;
use Core\Platform\Platform;
use Core\Util\Net;
use Think\Controller;
use Think\Log;

class AuthController extends Controller {
    
    public function requireAction() {
        $force = I('get.force') == 'true';
        $m = new Member();
        $m->auth();
        $member = C('MEMBER');
        
        if(IS_POST) {
            $post = inputRaw();
            if($m->update($member['uid'], $post)) {
                session('require:forward', null);
                exit('success');
            }
            exit('error');
        }

        $require = session('__:require');
        if(empty($require) || empty($member)) {
            $this->error('非法访问');
        }
        session('__:require', null);
        $forward = session('require:forward');
        if(empty($forward)) {
            $forward = U('/');
        } else {
            $forward = $forward;
        }
        
        $profiles = $m->profile($member['uid'], $require['fields']);
        $isEmpty = false;
        foreach($profiles as $p) {
            if(empty($p)) {
                $isEmpty = true;
                break;
            }
        }
        if(!$force && !$isEmpty) {
            redirect($forward);
        }
        $fields = Member::fields();
        $ds = array();
        foreach($require['fields'] as $field) {
            $row = $fields[$field];
            if(empty($row['icon'])) {
                $row['icon'] = 'edit';
            }
            $row['value'] = $profiles[$field];
            $ds[] = $row;
        }
        $this->assign('profiles', $profiles);
        $this->assign('ds', $ds);
        $this->assign('message', $require['message']);
        $this->assign('forward', $forward);
        $this->display('require');
    }

    public function registerAction() {
        if(IS_POST) {
            $fan = array();
            $post = inputRaw();
            if($post['from'] == 'weixin') {
                $fan = session('fan:weixin');
            }
            $input = array();
            $input['mobile'] = $post['mobile'];
            $input['password'] = $post['password'];
            $input['from'] = $post['from'];
            if(empty($input['mobile']) || empty($input['password'])) {
                exit('error');
            }
            $m = new Member();
            $ret = $m->create($input, $fan);
            if(is_error($ret)) {
                exit(json_encode($ret));
            }
            session('fan:weixin', null);
            $m->login($ret);
            exit('success');
        }
        exit('error');
    }
    
    public function weixinAction() {
        $code = I('get.code');
        if(!empty($code)) {
            Member::loadSettings();
            $setting = C('MS');
            $auth = $setting[Member::OPT_AUTH_WEIXIN];
            if($auth == '0') {
                exit('request error');
            } else {
                $a = new Account();
                $account = $a->getAccount($auth, Account::ACCOUNT_WEIXIN);
                $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$account['appid']}&secret={$account['secret']}&code={$code}&grant_type=authorization_code";
                $ret = Net::httpGet($url);
                if(!is_error($ret)) {
                    $auth = @json_decode($ret, true);
                    if(is_array($auth) && !empty($auth['openid'])) {
                        $condition = '`platformid`=:platform AND `openid`=:openid';
                        $pars = array();
                        $pars[':platform'] = $account['id'];
                        $pars[':openid'] = $auth['openid'];
                        $fan = $a->table('__MMB_MAPPING_FANS__')->where($condition)->bind($pars)->find();
                        if(empty($fan)) {
                            $platform = Platform::create($account['id']);
                            $info = $platform->fansQueryInfo($auth['openid'], true);
                            $fan = array();
                            $fan['platformid'] = $account['id'];
                            $fan['uid'] = 0;
                            $fan['openid'] = $auth['openid'];
                            if(!is_error($info)) {
                                if(!empty($info['original']['unionid'])) {
                                    $fan['unionid'] = $info['original']['unionid'];
                                    $uid = $a->table('__MMB_MAPPING_FANS__')->field('uid')->where("`unionid`=`{$info['original']['unionid']}`")->find();
                                    if(!empty($uid)) {
                                        $fan['uid'] = $uid;
                                    }
                                }
                                
                                $fan['subscribe'] = $info['original']['subscribe'];
                                $fan['subscribetime'] = $info['original']['subscribe_time'];
                                unset($info['original']);
                                $fan['info'] = serialize($info);
                            }
                            $fan['unsubscribetime'] = 0;
                            $a->table('__MMB_MAPPING_FANS__')->data($fan)->add();
                        }
                        
                        $stateKey = I('get.state');
                        $state = session('auth:forward');
                        session('auth:forward', null);
                        if($state[0] == $stateKey) {
                            $forward = $state[1];
                        } else {
                            $forward = U('/');
                        }
                        if(stripos($forward, '?') !== false) {
                            $forward .= '&wxref=mp.weixin.qq.com#wechat_redirect';
                        } else {
                            $forward .= '?wxref=mp.weixin.qq.com#wechat_redirect';
                        }

                        if(!empty($fan['uid'])) {
                            //登陆
                            $m = new Member();
                            $member = $m->profile($uid);
                            if(!empty($member)) {
                                $m->login($fan['uid']);
                                redirect($forward);
                            }
                        }
                        
                        if($setting[Member::OPT_POLICY] == Member::OPT_POLICY_CLASSICAL) {
                            //兼容模式, 创建新用户
                            $input = array();
                            $input['from'] = 'weixin';
                            $m = new Member();
                            $ret = $m->create($input, $fan);
                            if(!empty($ret)) {
                                $m->login($ret);
                                redirect($forward);
                            } else {
                                $this->error('访问错误');
                            }
                        } else {
                            //统一模式, 注册新用户
                            session('fan:weixin', $fan);
                            if(!empty($fan['info'])) {
                                $fan['info'] = unserialize($fan['info']);
                            }
                            $this->assign('fan', $fan);
                            $this->assign('forward', $forward);
                            $this->display('weixin');
                        }
                        return;
                    } else {
                        $this->error('微信授权失败错误信息为: ' . $ret);
                    }
                }
                $this->error('微信授权失败错误信息为: ' . $ret['message']);
            }
        }
        exit('访问错误');
    }
    
    public function basicAction() {
        $this->display('basic');
    }
}


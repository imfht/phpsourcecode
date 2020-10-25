<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommonAction
{
	public function index() {
		import('ORG.Util.RBAC');
    	if(!$_SESSION[C('USER_AUTH_KEY')]) 
    	{
            //跳转到认证网关
            redirect(U(C('USER_AUTH_GATEWAY')));
        }
        else
        {
        	$this->display();
        }
        
    }
	
	public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
			'PHP版本'=>PHP_VERSION,
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [<a href="http://thinkphp.cn" target="_blank">查看最新版本</a>]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名[IP]'=>$_SERVER['SERVER_NAME'].' ['.gethostbyname($_SERVER['SERVER_NAME']).']',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'OFF':'ON',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'OFF':'ON',
            );
			//print_r($info);
        $this->assign('info',$info);
        $this->display();
    }
	
	public function logout()
	{
		session_destroy();
		setcookie('leftid','');
		setcookie('topid','');
		setcookie('link','');
		redirect(U(C('USER_AUTH_GATEWAY')));
	}
	
	public function login()
	{
		if(IS_POST)
		{
			$username = I('username','',string);
			$password = I('password','',string);
			$code = I('code','',string);
			// || md5($code) != $_SESSION['verify']
			if(empty($username) || empty($password) )
			{
				$this->error('登陆失败!');
			}
			else
			{
				import('ORG.Util.RBAC');
				$map = array();
				$map['username'] = $username;
				$map['status'] = array('gt',0);
				$authInfo=RBAC::authenticate($map);
				if(empty($authInfo))
				{
					$this->error('账号不存在或者被禁用!');
				}
				else
				{
					if($authInfo['pwd'] != md5($password))
					{
						$this->error('账号密码错误!');
					}
					else
					{
						$_SESSION[C('USER_AUTH_KEY')] = $authInfo['uid'];
						$_SESSION['aliasname'] = $authInfo['aliasname'];

						if($authInfo['username'] == C('ADMIN_AUTH_KEY'))
						{
							session(C('ADMIN_AUTH_KEY'),true);
						}
						else
						{
							session(C('ADMIN_AUTH_KEY'),false);
						}
						RBAC::saveAccessList();
						redirect(U('Index/index'));
					}
				}
			}
			
		}
		else
			$this->display();
	}

}
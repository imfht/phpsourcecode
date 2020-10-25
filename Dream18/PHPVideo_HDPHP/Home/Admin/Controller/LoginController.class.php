<?php
/**
 * 后台登录控制器
 * @author:楚羽幽
 */
class LoginController extends Controller
{
	// 数据对象
	private $db;
	// 构造函数
	public function __init()
	{
		// 父级构造函数
		parent::__init();

		// 设置页面编码
		header('Content-Type:text/html;charset=utf-8');

		// 连接数据库数据
		$this->db = M('user');
	}

	/**
	 * 「PHP联盟」
	 * [登录检测 视图模板]
	 * @return [type] [description]
	 * 楚羽幽 <Name_Cyu@Foxmail.com>
	 */
	public function index()
	{
		if(session('uid'))
		{
			$this->success('你已经登录，正在跳转到后台。','Admin/Index/index');
		}
		$this->display();
	}

	/**
	 * 登录数据验证
	 * @return [type] [description]
	 * 楚羽幽 <Name_Cyu@Foxmail.com>
	 */
	public function login()
	{
		if(IS_POST)
		{
			// Q函数接收username
			$user = Q('post.username');

			// 验证登录账户
			if(!$user = $this->db->where(array('username'=> $user))->find())
			{
				$this->error('账户不存在，请重新输入。','index');
			}

			// 验证登录密码
			if($user['password'] != md5($_POST['password']))
			{
				$this->error('密码错误，请重新输入。','index');
			}

			// 判断登录权限
			/*if($username['role_id'] != 1 || $username['role_id'] != 2)
			{
				$this->error('没有权限进行登录！', U('index'));
			}*/

			// 是否被锁定
			if(!$user['user_status'])
			{
				$this->error('账户被锁定，无法登录！');
			}

			// 收集登录信息
			$data = array(
				'uid'		=> $user['uid'],
				'lastip'	=> ip_get_client() ,
				'logintime'	=> time(),
			);

			// 更新登录信息
			$this->db->save($data);


			// 账户密码验证成功，赋值SESSION
			$_SESSION['uid'] = $user['uid']; 
			$_SESSION['username'] = $user['username']; 
			$_SESSION['nickname'] = $user['nickname'];
			// 跳转到后台首页
			$this->success('登录成功！正在跳转到后台。','Admin/Index/index');
		}
	}

	/**
	 * 退出动作
	 * @return [type] [description]
	 * 楚羽幽 <Name_Cyu@Foxmail.com>
	 */
	public function out()
	{
		// 设置SESSION里面的值为NULL
		session(NULL);
		// 退出成功后调转
		$this->success('退出成功，调转到登录页面。','index');
	}

	/*------------------------------------属性定义--------------------------------------------*/

	/**
	 * 验证码视图
	 * @return [type] [description]
	 */
	public function code()
	{
	}

	/**
     * 获取用户密码加密key
     * @return string
     */
    public function getUserCode()
    {
        return substr(md5(C("AUTH_KEY") . mt_rand(1, 1000) . time() . C('AUTH_KEY')), 0, 10);
    }
}
?>
<?php

namespace backend\controllers;

use Yii;
use common\helpers\ArrayHelper;

/**
 * 基础控制器类
 * 
 * @property int $uid 用户uid
 * @property \yii\web\User $user 用户登录对象
 * 
 * @author ken <vb2005xu@qq.com>
 */
class BaseController extends \yii\web\Controller
{

	/**
	 * @inheritdoc
	 */
	public $enableCsrfValidation = false;

	/**
	 * 请求对象
	 * @var \yii\web\Request 
	 */
	public $request = null;

	/**
	 * 响应对象
	 * @var \yii\web\Response
	 */
	public $response = null;

	/**
	 * 当前登录的用户对象
	 * @var yii\web\User
	 */
	private $_user = null;

	/**
	 * 视图变量
	 * @var array 
	 */
	protected $_data = [];

	/**
	 * js变量
	 * @var array
	 */
	public $jsvars = [];

	/**
	 * 授权规则配置
	 * @var array
	 */
	private $_access_rules = [];

	/**
	 * 授权规则生效的动作
	 * @var array
	 */
	private $_access_only = [];

	/**
	 * 动作执行后
	 * @param type $action
	 * @param type $result
	 */
	public function afterAction($action, $result)
	{
		if (!$this->request->isGet)
		{
			$type = $this->request->method;
			$domain = $this->request->ServerName;
			$url = $this->request->url;
			$params = \yii\helpers\Json::encode($this->post());
			$controller = $this->id;
			$action = $this->action->id;
			$ip = $this->request->userIP;
			\backend\models\AdminHistory::addHistory($this->uid, $type, $domain, $url, $params, $controller, $action, $ip);
		}
		return parent::afterAction($action, $result);
	}

	/**
	 * 执行前
	 * @param type $action
	 * @return boolean
	 */
	public function beforeAction($action)
	{
		//父类是否执行成功
		if (!parent::beforeAction($action))
		{
			return false;
		}

		//判断是否有其他错误
		if (!$this->action instanceof \yii\base\InlineAction)
		{
			return true;
		}

		//判断是否管理员用户, 管理员用户默认拥有所有权限
		if ($this->user->identity && $this->user->identity->isAdmin())
		{
			return true;
		}

		//如果是游客，并且请求的是登录页，则有权限访问
		if ($this->user->isGuest && in_array($this->action->uniqueId, ['site/login', 'site/index']))
		{
			return true;
		}


		//如果是登录用户，并且请求的是注销页，则有权限访问
		if (!$this->user->isGuest && in_array($this->action->uniqueId, ['site/logout']))
		{
			return true;
		}

		//是否有访问权限
		if ($this->user->can($this->action->uniqueId))
		{
			return true;
		}
		//api访问和普通访问
		if (substr($this->id, 0, 3) == 'api')
		{
			$data = $this->failure(\Yii::t('yii', 'You are not allowed to perform this action.'), 99);
			echo(json_encode($data));
		}
		else
		{
			echo $this->redirectMessage(\Yii::t('yii', 'You are not allowed to perform this action.'));
		}
		exit();
	}

	/**
	 * 构造函数
	 * @param type $id
	 * @param type $module
	 * @param array $config
	 */
	function __construct($id, $module, $config = [])
	{
		parent::__construct($id, $module, $config = []);
		$this->_init();
	}

	/**
	 * 初始化
	 */
	private function _init()
	{
		$this->_user = Yii::$app->user;
		$this->request = Yii::$app->request;
		$this->response = Yii::$app->response;
		Yii::$app->session->open();
		$this->jsvars['site_url'] = \Yii::$app->params['site_url'];
	}

	/**
	 * 获取当前用户的uid
	 * @return int
	 */
	public function getUid()
	{
		return $this->_user->id;
	}

	/**
	 * 获取当前用户信息
	 * @return \yii\web\User
	 */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * 获取一个指定的参数
	 * @param string $key 参数名称
	 * @param string $default 如果参数不存在的默认值
	 * @return mixd
	 */
	public function post($key = null, $default = null)
	{
		return $this->request->post($key, $default);
	}

	/**
	 * 获取多个指定的参数
	 * @param array $keys
	 * @return array
	 */
	public function only(array $keys)
	{
		$post = $this->request->post();
		return ArrayHelper::onlyValue($post, $keys);
	}

	/**
	 * 成功信息
	 * @param array $data 成功后返回的数据
	 * @return array
	 */
	public function success($data = array())
	{
		$this->response->format = \yii\web\Response::FORMAT_JSON;
		return $data;
	}

	/**
	 * 失败信息
	 * @param string $msg 错误信息
	 * @param int $code 错误代码
	 * @return array
	 */
	public function failure($msg, $code = 99, $status = 400)
	{
		$this->response->format = \yii\web\Response::FORMAT_JSON;
		$data = array(
			'errcode' => $code,
			'errmsg' => $msg
		);
		return $data;
	}

	/**
	 * 显示一个提示页面，然后重定向浏览器到新地址
	 *
	 * @param string $message 信息
	 * @param string $url 跳转url
	 * @param string $caption 标题
	 * @param int $delay 是否延时自动跳转
	 * @param string $script 附加脚本
	 *
	 * @return string
	 */
	public function redirectMessage($message, $url = '/', $caption = '提示', $viewname = '/site/message', $delay = 5, $script = '')
	{
		$data = array(
			'caption' => $caption,
			'message' => $message,
			'url' => $url,
			'delay' => $delay,
			'script' => $script,
		);
		$response = $this->render($viewname, $data);
		return $response;
	}

	/**
	 * 获取cookie
	 * @param string $name
	 * @return string
	 */
	public function getCookie($name)
	{
		return $this->request->cookies->getValue($name);
	}

	/**
	 * 添加cookie
	 * @param string $name
	 * @param string $value
	 */
	public function addCookie($name, $value, $expire = 0)
	{
		$this->response->cookies->add(new \yii\web\Cookie([
			'name' => $name,
			'value' => $value,
//			'expire' => $expire,
		]));
	}

	/**
	 * 删除cookie
	 * @param string $name
	 */
	public function delCookie($name)
	{
		$this->response->cookies->remove($name);
	}

}

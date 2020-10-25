<?php



abstract class OAuth2_Provider {

	/**
	 * @var  string  供应商名称
	 */
	public $name;

	/**
	 * @var  string  名字
	 */
	public $human;

	/**
	 * @var  string  开始标识
	 */
	public $state_key = 'state';

	/**
	 * @var  string  错误标识
	 */
	public $error_key = 'error';

	/**
	 * @var  string
	 */
	public $client_id_key = 'client_id';

	/**
	 * @var  string
	 */
	public $client_secret_key = 'client_secret';

	/**
	 * @var  string
	 */
	public $redirect_uri_key = 'redirect_uri';

	/**
	 * @var  string
	 */
	public $access_token_key = 'access_token';

	/**
	 * @var  string
	 */
	public $uid_key = 'uid';
	/**
	 * @var  string
	 */
	public $nick_key = 'nickname';

	/**
	 * @var  string
	 */
	public $callback;

	/**
	 * @var  array
	 */
	protected $params = array();

	/**
	 * @var  string
	 */
	protected $method = 'GET';

	/**
	 * @var  string
	 */
	protected $scope;

	/**
	 * @var  string  分隔符
	 */
	protected $scope_seperator = ',';

	/**
	 * 重载默认的类的属性的选项
	 *
	 * @param   array 提供商配置
	 * @throws  如果没有提供必要的选项抛出异常
	 */
	public function __construct(array $options = array()) {
		// 供应商名称就是类的名称
		if (!$this->name) {
			$this->name = strtolower(substr(get_class($this), strlen('OAuth2_Provider_')));
		}
		// App Key不存在，抛出异常
		if (empty($options['key'])) {
			throw new Exception('缺少关键参数: app Key');
		}
		$this->client_id = $options['key'];
		isset($options['callback']) and $this->callback = $options['callback'];
		isset($options['secret']) and $this->client_secret = $options['secret'];
		isset($options['scope']) and $this->scope = $options['scope'];
		// 回调地址
		$this->redirect_uri = $options['url'];
	}

	/**
	 * $url = $provider->url_authorize();
	 * @return  string
	 */
	abstract public function url_authorize();

	/**
	 * $url = $provider->url_access_token();
	 * @return  string
	 */
	abstract public function url_access_token();

	/**
	 * @param OAuth2_Token_Access $token
	 * @return array 用户数据信息
	 */
	abstract public function get_user_info(OAuth2_Token_Access $token);

	/*
	 * 获得授权的代码提供者服务,重定向到供应商授权页面
	 */
	public function authorize($options = array()) {
		// 定义一个状态值
		$state = md5(uniqid(rand(), true));
		get_instance()->session->set_userdata('state', $state);
		// 请求参数设定
		if ($this->human == 'weixin') {
			$params = array(
				$this->client_id_key => $this->client_id,
				$this->redirect_uri_key => isset($options[$this->redirect_uri_key]) ? $options[$this->redirect_uri_key] : $this->redirect_uri,
				'response_type' => 'code',
				'scope' => is_array($this->scope) ? implode($this->scope_seperator, $this->scope) : $this->scope,
				$this->state_key => $state.'#wechat_redirect',
			);
		} else {
			$params = array(
				$this->client_id_key => $this->client_id,
				$this->redirect_uri_key => isset($options[$this->redirect_uri_key]) ? $options[$this->redirect_uri_key] : $this->redirect_uri,
				$this->state_key => $state,
				'response_type' => 'code',
				'scope' => is_array($this->scope) ? implode($this->scope_seperator, $this->scope) : $this->scope,
			);
		}

		$params = array_merge($params, $this->params);
		// 跳转到授权登录页面
		$url = $this->url_authorize().'?'.http_build_query($params);

		redirect($url);
	}

	/*
	 * 访问API
	 *
	 * @param	string	code URL中传递过来的code值
	 * @return	object
	 */
	public function access($code, $options = array()) {
		// 验证状态值是否一致
		if (isset($_GET[$this->state_key]) AND $_GET[$this->state_key] != get_instance()->session->userdata('state')) {
			throw new OAuth2_Exception("状态不匹配，小心账号被恶意网站盗用");
		}
		// 组装配置参数获取返回数据
		$params = array(
			$this->client_id_key => $this->client_id,
			$this->client_secret_key => $this->client_secret,
			'grant_type' => isset($options['grant_type']) ? $options['grant_type'] : 'authorization_code',
		);
		$params = array_merge($params, $this->params);
		switch ($params['grant_type']) {
			case 'authorization_code':
				$params['code'] = $code;
				$params[$this->redirect_uri_key] = isset($options[$this->redirect_uri_key]) ? $options[$this->redirect_uri_key] : $this->redirect_uri;
				break;
			case 'refresh_token':
				$params['refresh_token'] = $code;
				break;
		}
		// 请求的地址
		$url = $this->url_access_token();

		$response = null;
		switch ($this->method) {
			case 'GET':
				$url .= '?'.http_build_query($params);
				$response = file_get_contents($url);
				$return = $this->parse_response($response);
				break;
			case 'POST':
				if (function_exists('curl_init')) { // curl方式
					$oCurl = curl_init();
					if (stripos($url, 'https://') !== FALSE) {
						curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
						curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
					}
					$aPOST = array();
					foreach ($params as $key => $val){
						$aPOST[] = $key.'='.urlencode($val);
					}
					curl_setopt($oCurl, CURLOPT_URL, $url);
					curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($oCurl, CURLOPT_POST, TRUE);
					curl_setopt($oCurl, CURLOPT_POSTFIELDS, join('&', $aPOST));
					$response = curl_exec($oCurl);
					curl_close($oCurl);
					$return = $this->parse_response($response);
				} elseif (function_exists('stream_context_create')) { // php5.3以上
					$opts = array(
						'http' => array(
							'method' => 'POST',
							'header' => 'Content-type: application/x-www-form-urlencoded',
							'content' => http_build_query($params),
						)
					);
					$_default_opts = stream_context_get_params(stream_context_get_default());
					$context = stream_context_create(array_merge_recursive($_default_opts['options'], $opts));
					$response = file_get_contents($url, false, $context);
					$return = $this->parse_response($response);
				} else {
					// 服务器不支持，抛出异常
					throw new OAuth2_Exception('服务器必须开启CURL扩展');
				}
				break;
			default:
				throw new OutOfBoundsException('提交方式必须选择POST或者GET');
		}
		// 判断返回值，抛出异常
		if (!empty($return[$this->error_key]) OR !isset($return['access_token'])) {
			throw new OAuth2_Exception("<br>请求地址：$url<br>返回信息：$response");
		}
		// 
		$return['uid_key'] = $this->uid_key;
		$return['nick_key'] = $this->nick_key;
		$return['access_token_key'] = $this->access_token_key;
		switch ($params['grant_type']) {
			case 'authorization_code':
				return OAuth2_Token::factory('access', $return);
				break;
			case 'refresh_token':
				return OAuth2_Token::factory('refresh', $return);
				break;
		}
	}

	/*
	 * 返回结果转为数组
	 *
	 * @param	string
	 * @return	array	
	 */
	protected function parse_response($response = '') {
		if (strpos($response, 'callback') !== false) {
			$lpos = strpos($response, '(');
			$rpos = strrpos($response, ')');
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
			$return = json_decode($response, true);
		} elseif (strpos($response, '&') !== false) {
			parse_str($response, $return);
		} else {
			$return = json_decode($response, true);
		}
		return $return;
	}
}
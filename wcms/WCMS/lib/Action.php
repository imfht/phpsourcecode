<?php
/**
 * 控制器  初始化smarty
 * @author wolf [Email: 116311316@qq.com]
 * @link
 */
abstract class Action {
	const SUCEESS = 'success';
	const ERROR = 'error';
	private $premission = true;
	/**
	 * 存储视图
	 *
	 * @var View
	 */
	protected $moduletemp = ''; // 公共模板
	protected $_controllerName = '';
	protected $_actionName = '';
	private $isLoadLang = false;
	static $logService;
	static $pluginService;

	/**
	 * 获取视图
	 *
	 * @return View
	 */
	public function __construct(Route $request) {
		$this->_controllerName = $request->getControllerName ();
		$this->_actionName = $request->getActionName ();
		$this->_init ();
		$rs = $this->_routeDown ();
		if (is_string ( $rs ) && $rs != self::SUCEESS) {
			$this->premission = false;
			$this->authorizeTemp ( $rs );
			return;
		}
		$sys=new SysService();
		$config=$sys->getConfig();
		$this->view()->assign('config',$config);
	}

	public function authorizeTemp($error) {
		$this->view ()->assign ( 'error', $error );
		$this->view ()->display ( 'file:public/error.tpl' );
	}


	public function getPremission() {
		return $this->premission;
	}
	/**
	 * 获取当前控制器
	 *
	 * @return Controller
	 */
	protected function getControllerName() {
		return $this->_controllerName;
	}

	//插件注册器
	public static function getPluginService($filter) {
		if (self::$pluginService [$filter] == null) {
			self::$pluginService [$filter] = new PluginService ( $filter );
		}
		return self::$pluginService [$filter];
	}

	//日志注册器
	public static function getLogService() {
		if (self::$logService == null) {
			self::$logService = new LogService ();
		}
		return self::$logService;
	}

	/**
	 * 加载多语言
	 * Enter description here ...
	 */
	private function loadLang() {
	}
	/**
	 * 获取当前方法
	 *
	 * @return Action
	 */
	protected function getActionName() {
		return $this->_actionName;
	}
	/**
	 * 子类自定义
	 */
	protected function _init() {
	}
	/**
	 * 路由结束
	 */
	protected function _routeDown() {
	}
	/**
	 * 自定义模板路径
	 *
	 * @param string $path
	 */
	protected function setViewPath($path) {
		return $this->moduletemp = $path;
	}
	/**
	 * 得到自定义模板路径
	 */
	protected function getViewPath() {
		return $this->moduletemp;
	}
	/**
	 * 设置smarty视图
	 *
	 * @return View
	 */
	protected function view() {

		//只有加载视图的时候 才会记载语言包   放置一个action内多次加载
		return View::getInstance (); //每次都需要初始化
	}
	/**
	 * 返回json格式
	 *
	 * @param unknown_type $error
	 * @param unknown_type $data
	 * @param unknown_type $status
	 */
	protected function sendNotice($error, $data = NULL, $status = FALSE) {
		$res = array ('status' => $status, 'message' => $error, 'data' => $data );
		echo json_encode ( $res );
		exit ();
	}

	/**
	 * 全角转半角
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $str
	 */
	protected function semiangle($str) {
		$arr = array ('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T', 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']', '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-', '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.', '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"', '　' => ' ' );
		return strtr ( $str, $arr );
	}
	protected function savelog($content, $sql) {
		$log = ROOT . 'log' . DIRECTORY_SEPARATOR . date ( "Ymd", time () ) . '.txt';
		$handle = fopen ( $log, "a+" );
		$content = '[' . date ( "Y-m-d H:i:s", time () ) . ']    ' . $content . ' ' . $sql . "\n";
		fwrite ( $handle, $content );
		fclose ( $handle );
	}

	/**
	 * 登录跳转
	 *
	 * @param
	 * $message
	 * @param
	 * $jumpUrl
	 * @param
	 * $waitSecond
	 */
	protected function redirect($message, $jumpUrl, $waitSecond = 2, $status = TRUE) {
		$this->view ()->assign ( 'message', $message );
		$this->view ()->assign ( 'jumpUrl', $jumpUrl );
		$this->view ()->assign ( 'waitSecond', $waitSecond );
		$this->view ()->display ( 'file:public/success.tpl' );
		exit ();
	}
}

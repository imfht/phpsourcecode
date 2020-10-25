<?php
/**
 * 日志处理器  
 * @author wolf
 * 以姓名,事件名来进行统计
 *
 */
class LogService{
	private $_pageNum = 50;
	private $_level = array (0 => "一般", 1 => "重要" );
	/* (non-PHPdoc)
	 * @see ILog::getLogByActionTime()
	 */
	public function getLogByActionTime($actionName) {
	}
	//获取时间相关人
	public function getLogByEvent($event, $limit) {
		return LogModel::instance ()->getLogByWhere ( array ('event' => $event ), $limit );
	}
	/**
	 * 最近30天的登录情况 图表显示
	 */
	public function chart($event) {
		$event = urldecode ( $event );
		$days = isset ( $_GET ['days'] ) ? $_GET ['days'] : 7;
		$format = isset ( $_GET ['format'] ) ? $_GET ['format'] : "d";
		return $this->getLogStatus ( $format, $days, $event );
	}
	/**
	 * 获取用户的登录状态  用与图表显示
	 * Enter description here ...
	 * @param string $format 日 月 
	 * @param int $days 统计几天
	 */
	public function getLogStatus($format, $days, $event) {
		switch ($format) {
			case "m" :
				$f = "%Y%m";
				break;
			case "d" :
				$f = "%Y%m%d";
				break;
			default :
				$f = "%Y%m%d";
				break;
		}
		$rs = LogModel::instance ()->tjLogin ( $days, $f, $event ); //统计30的数据
		foreach ( $rs as $k => $v ) {
			$arr [$k] ['rq'] = date ( "n月j日", strtotime ( $v ['action_time'] ) );
			$arr [$k] ['num'] = $v ['num'];
		}
		return array_reverse ( $arr );
	}
	public function getEventFlag() {
		$yesterday = date ( "Y-m-d", strtotime ( "-1 days" ) );
		return LogModel::instance ()->getYesterdayEvent ( $yesterday );
	}
	public function listing($p) {
		$p = $p == 0 ? 1 : $p;
		$totalNum = LogModel::instance ()->countLogNum ();
		$page = $this->page ( $totalNum, $p, $this->_pageNum );
		//超过了分页数
		if (! $page) {
			return false;
		}
		//获取基础数据
		$logList = LogModel::instance ()->getLogPage ( $page ['start'], $this->_pageNum );
		return array ('list' => $logList, 'totalnum' => $totalNum, 'page' => $page );
	}
	public function add($username, $event, $level = 0) {
		$param = array ('username' => $username, 'event' => $event, 'level' => $level );
		return LogModel::instance ()->addLog ( $param );
	}
	//添加管理员事件
	public function addAdminEvent($uid, $event) {
		$member = new MemberService ();
		$user = $member->getMemberByUid ( $uid );
		$this->add ( $user ['real_name'], $event, 1 );
	}
	public function delMoreLog() {
		$sys = new SysService ();
		$config = $sys->getConfig ();
		$day = "-" . $config ['log'] . " days";
		$now = strtotime ( date ( "Ymd", time () ) );
		$time = date ( "Y-m-d", strtotime ( $day, $now ) );
		LogModel::instance ()->delLog ( $time );
	}
	/**
	 * 查找名字
	 * @see ILog::getLogByUsername()
	 */
	public function getLogByUsername($username, $level = 0) {
		return LogModel::instance ()->getLogByWhere ( array ('username' => $username, 'level' => $level ), 100 );
	}
	/**
	 * 分页
	 *
	 * @return void
	 */
	private function page($total, $pageid, $num) {
		$pageid = isset ( $pageid ) ? $pageid : 1;
		$rs = $pageid * $num - $total;
		$start = ($pageid - 1) * $num;
		$pagenum = ceil ( $total / $num );
		/*修正分类不包含内容 显示404错误*/
		$pagenum = $pagenum == 0 ? 1 : $pagenum;
		/*如果超过了分类页数 404错误*/
		if ($pageid > $pagenum) {
			header ( "HTTP/1.1 404 Not Found" );
		}
		$page = array ('start' => $start, 'num' => $num, 'current' => $pageid, 'page' => $pagenum );
		return $page;
	}
}
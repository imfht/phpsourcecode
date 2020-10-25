<?php
/**
 * 系统设置
 * @author 齐迹  email:smpss2012@gmail.com
 *
 */
class c_system extends base_c {
	function __construct($inPath) {
		parent::__construct ();
		if (self::isLogin () === false) {
			$this->ShowMsg ( "请先登录！", $this->createUrl ( "/main/index" ) );
		}
		if (self::checkRights ( $inPath ) === false) {
			//$this->ShowMsg("您无权操作！",$this->createUrl("/system/index"));
		}
		$this->params ['inpath'] = $inPath;
		$this->params ['head_title'] = "系统-" . $this->params ['head_title'];
	}
	function pageindex($inPath) {
		$this->params['system'] = $this->systemCount();
		return $this->render ( 'system/index.html', $this->params );
	}
	
	function pagesetting($inPath) {
		$modelObj = new base_m ();$post = base_Utils::shtmlspecialchars ( $_POST );
		if(!$_POST){
			$this->params ['system_name'] =base_Constant::DEFAULT_TITLE;
			$this->params ['rewrite'] =(int)base_Constant::REWRITE;
			$this->params ['cookie_key'] =base_Constant::COOKIE_KEY;
			$this->params ['temp_dir'] =base_Constant::TEMP_DIR;
			$this->params ['theme'] =base_Constant::THEME;
			$this->params ['ratio'] =base_Constant::RATIO;
			$this->params ['tormb'] =base_Constant::TORMB;
			$this->params ['barcode'] =base_Constant::BARCODE;
		}else{
			if($_POST['cleartable']==1){
				$tableArr = array("category","goods","member","purchase","sales","log","membercache","excredit");
				if(!$modelObj -> clearTable($tableArr)){
					$this->showMsg("清空数据出错！原因:".$modelObj -> getError());
				}
			}
			 include ROOT_APP."/base/config.php";

			$system_name = base_Utils::getStr($_POST['system_name']);
			if($system_name){
				$config['DEFAULT_TITLE'] =$system_name;
			}
			$barcode = $_POST['barcode'];
			if(preg_match ("/[a-zA-Z0-9]/", $barcode)&strlen($barcode)==7){
				$config['BARCODE'] =$barcode;
			}
			$cookie_key = base_Utils::getStr($_POST['cookie_key']);
			if($cookie_key != $config['COOKIE_KEY']){
				$cookie_key = md5($cookie_key);
				$config['COOKIE_KEY'] =$cookie_key;
			}
			$rewrite = base_Utils::getStr($_POST['rewrite'],'int');
			if($rewrite==1){
				$config['REWRITE'] ='TRUE';
			}else{
				$config['REWRITE'] ='FALSE';
			}
			if (in_array ($_POST['theme'], array ("red", "green", "blue"))) { 
			$config['THEME'] =$_POST['theme'];
			}
			if(is_numeric($_POST['ratio'])){
			$config['RATIO'] =$_POST['ratio'];
			}
			if(is_numeric($_POST['tormb'])){
			$config['TORMB'] =$_POST['tormb'];
			}
$base='<?php '."\n".'$config=array('."\n";
foreach($config as $key=>$value)
{
	$base.='"'.$key.'"=>"'.$value.'",'."\n";
}
$base=rtrim(trim($base),',');
$base.="\n".'); ?>';
			$f = @fopen(ROOT_APP."/base/config.php", "w");
			if($f){
				fwrite($f, $base);
				fclose($f);
			}else{
				$this->ShowMsg("没有写权限");
			}
			$settingbase='<?php class base_Constant {';
			foreach($config as $key=>$value)
			{if(stristr($value,'false')){$value=false;}elseif(stristr($value,'true')){$value=true;}
				$settingbase.='const '.$key.'="'.$value.'";';
			}
			$settingbase.='} ?>';
			$f = @fopen(ROOT_APP."/base/Constant.class.php", "w");
			if($f){
				fwrite($f, $settingbase);
				fclose($f);
			}else{
				$this->ShowMsg("没有写权限");
			}
			$this->showMsg("修改成功",$this->createUrl("/system/setting"),2,1);
			//$this->redirect($this->createUrl("/system/setting"));
		}
		return $this->render ( 'system/setting.html', $this->params );
	}
	
	function pagerights($inPath) {
		$url = $this->getUrlParams ( $inPath );
		$groupObj = new m_group ();
		$gid = ( int ) $url ['gid'];
		$this->params ['gid'] = $gid;
		if (! $gid) {
			$this->params ['group'] = $groupObj->select ()->items;
			
			return $this->render ( 'system/rights.html', $this->params );
		} else {
			if (! $_POST) {$post = base_Utils::shtmlspecialchars ( $_POST );
				if ($gid) {
					$this->params ['rights'] = $groupObj->selectOne ( "gid = {$gid}" );
					$this->params ['action'] = unserialize ( $this->params ['rights'] ['action_code'] );
					return $this->render ( 'system/rightsshow.html', $this->params );
				}
				$this->ShowMsg ( "用户组不存在！" );
			} else {
				$action_code = $this->creatRights ( $_POST );
				$groupObj->update ( "gid = {$gid}", "action_code = '{$action_code}'" );
				$cacheName = "action_code_group_" . $gid;
				$cache = SCache::getCacheEngine ( 'file' );
				$cache->init ( array ("dir" => SlightPHP::$appDir . "/cache", "depth" => 3 ) );
				$rs = $cache->del ( $cacheName );
				$this->ShowMsg ( "编辑成功！", $this->createUrl ( '/system/rights' ), '', 1 );
			}
		}
	}
	
	function pageaddrights($inPath) {
		if (! $_POST ['group_name']) {
			return $this->render ( 'system/addrights.html', $this->params );
		} else {
			$item = array ();
			$item ['group_name'] = base_Utils::shtmlspecialchars ( $_POST ['group_name'] );
			if ($item ['group_name']) {
				$groupObj = new m_group ();
				$res = $groupObj->selectOne ( "group_name='{$item['group_name']}'", 'gid' );
				if ($res)
					$this->ShowMsg ( '用户组名称已经存在！' );
				$item ['action_code'] = $this->creatRights ( $_POST );
				$rs = $groupObj->insert ( $item );
				if ($rs) {
					$this->ShowMsg ( '添加成功', $this->createUrl ( '/system/rights' ), '', 1 );
				} else {
					$this->ShowMsg ( '添加失败，请重试！错误原因：' . $groupObj->getError () );
				}
			}
			$this->ShowMsg ( '用户组名称不能够为空！' );
		}
	}
	function pagedbm($inPath){
		$url = $this->getUrlParams ( $inPath );
		$page = $url ['page'] ? ( int ) $url ['page'] : 1;

		return $this->render ( 'system/dbm.html', $this->params );
		}
	function pagelog($inPath){
		$url = $this->getUrlParams ( $inPath );
		$page = $url ['page'] ? ( int ) $url ['page'] : 1;
		$type = ( int ) $url ['type'];
		$ymd = date ( "Y-m-d", time () );
		$condi = "type={$type}";
		if ($_POST) {$post = base_Utils::shtmlspecialchars ( $_POST );
			$stime = base_Utils::getStr ( $_POST ['stime'] );
			$etime = base_Utils::getStr ( $_POST ['etime'] );
			if ($stime) {
				$etime = $etime ? $etime : $ymd;
				$condi .= " and dateymd between '{$stime}' and '{$etime}'";
			}
		}
		$logObj = new m_log();
		$logObj->setCount ( true );
		$logObj->setPage ( $page );
		$logObj->setLimit ( base_Constant::PAGE_SIZE );
		$rs = $logObj->select ( $condi, "", "", "order by log_id desc" );
		$this->params ['log'] = $rs->items;
		$this->params ['stime'] = $stime;
		$this->params ['etime'] = $etime;
		$this->params ['type'] = $type;
		$this->params ['pagebar'] = $this->PageBar ( $rs->totalSize, base_Constant::PAGE_SIZE, $page, $inPath );
		return $this->render ( 'system/log.html', $this->params );
	}
	
	private function creatRights($post) {
		$post = ( array ) base_Utils::shtmlspecialchars ( $post );
		$action = $menu = array ();
		foreach ( $post as $key => $val ) {
			if (in_array ( $key, array ('system', 'account', 'member', 'category', 'goods', 'purchase', 'sales', 'statistics' , 'shop' ) )) {
				$_temp = array ();
				foreach ( $val as $v ) {
					$vArr = explode ( ':', $v );
					$_temp [$vArr [1]] = $vArr [0];
					$action [] = $key . '_' . $vArr [1];
				}
				$menu [$key] = $_temp;
			}
		}
		return serialize ( array ('all' => 0, 'action' => $action, 'menu' => $menu ) );
	}
	
	private function systemCount() {
		$modelObj = new base_m ();
		$goodscount = $modelObj->_db->select ( base_Constant::TABLE_PREFIX . "goods", "", "count(1) as num" )->items; //商品总数
		$salesall = $modelObj->_db->select ( base_Constant::TABLE_PREFIX . "goods", "", "sum(countamount) as cm,sum(salesamount) as sm" )->items; // 总销售情况 和总的库存金额
		$modelObj->_db->setLimit(8);
		$goodsstock = $modelObj->_db->select ( base_Constant::TABLE_PREFIX . "goods", "stock<=warn_stock", "goods_id,goods_name,stock" )->items; //缺少库存的商品
		$modelObj->_db->setLimit(8);
		$salestop = $modelObj->_db->select ( base_Constant::TABLE_PREFIX . "sales", "sales_type=0 ", " sum( num ) AS a, goods_name,goods_id", "group by goods_id", "order by a desc" )->items; //销售排行榜
		$arr ['goodscount'] = $goodscount [0] ['num'];
		$arr ['goodsstock'] = $goodsstock;
		$arr ['salestop'] = $salestop;
		$arr ['salesall'] = $salesall [0];
		return $arr;
	}
}
?>
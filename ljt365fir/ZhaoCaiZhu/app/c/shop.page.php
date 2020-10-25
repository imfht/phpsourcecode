<?php 
/**
 * 登录
 * @author 齐迹  email:smpss2012@gmail.com
 *
 */
class c_shop extends base_c {
	function __construct($inPath) {
		parent::__construct ();
		if (self::isLogin () === false) {
			$this->ShowMsg ( "请先登录！", $this->createUrl ( "/main/index" ) );
		}
		if (self::checkRights ( $inPath ) === false) {
			//$this->ShowMsg("您无权操作！",$this->createUrl("/system/index"));
		}
		$this->params['inpath'] = $inPath;
		$this->params ['head_title'] = "爱麦商城-" . $this->params ['head_title'];
	}
	
	function pageindex($inPath){
		$url='http://www.arch.com/list.php?catid=36&ajax=1';  
		$html=file_get_contents($url);  
		$this->params['html'] = $html;
		return $this->render ( 'shop/index.html', $this->params );
	}
	
}
?>
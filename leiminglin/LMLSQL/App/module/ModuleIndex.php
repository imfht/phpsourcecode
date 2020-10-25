<?php
class ModuleIndex extends LmlBase{
	public function index(){
		if( !headers_sent() ) {
			header("Content-type:text/html;charset=utf-8");
		}

		if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] != C_ACTION){
			return $this->sqlExec();
		}

		if(IS_CLI){
			echo 'Welcome to use LMLPHP!';
		}else{
			echo '<div style="margin-top:100px;line-height:30px;font-size:16px;font-weight:bold;font-family:微软雅黑;text-align:center;color:red;">^_^,&nbsp;Welcome to use LMLPHP!<div style="color:#333;">A fully object-oriented PHP framework, keep it light, magnificent, lovely.</div></div>';
		}
	}

	private function sqlExec(){

		try {
			$dbconfig = require APP_PATH.'conf/dbconfig.php';

			$identifier = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
			if(in_array($identifier, array_keys($dbconfig))){
				$db = MysqlPdoEnhance::getInstance($dbconfig[$identifier]);
				$sql = isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:'';
				$rs = $db->query($sql);
				var_dump($rs);
			}else{
				echo $identifier . ' not found!';
			}

		} catch (LmlException $e) {
			echo($e->getMessage());
		}

	}

}

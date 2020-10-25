<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class IndexAction extends CommonAction {
		// 框架首页
		public function index() {
				$Comment = M("Comment");
				$Category = M("Category");
				$templist = $Comment -> where("1") -> order("create_time DESC") -> limit(5) -> select();
				foreach($templist as $val){
					$commentlist[$val['id']] = $val;
				}
				$catlist = $Category -> where("pid=0 AND level=0") -> order("id ASC") -> group("module") -> select();
				foreach($catlist as $val){
					$catcount[$val['module']] = $val;
				}
    		if($license = checkLicense()){
    			$license = '<font color="red">已授权 [ '.$license.' ]</font>';
    		}else{
    			$license = '<font color="blue">试用版，正式建站须</font> <a href="http://seophp.taobao.com/" target="_blank" title="点击购买商业授权"><font color="red">[购买授权]</font></a>';
    		}
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
//          '商业授权' => $license,
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
        );
        $this->assign('commentlist',$commentlist);
        $this->assign('catcount',$catcount);
        $this->assign('info',$info);
				$this->display();
		}
		// 首页
		public function main() {
				// 统计数据
				$this->display();
		}

}
?>
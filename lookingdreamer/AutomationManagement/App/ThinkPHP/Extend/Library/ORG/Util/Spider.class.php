<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class Spider{
	
		public $Spiders = array('baidu', 'google', 'sogou', 'soso', 'yahoo', 'bing');
		public $modules = array('new', 'product', 'down', 'case', 'job', 'blog');
		public $LastLogs;
		public $domain;
		public $linkdomain;
		public $spider;
		public $logfile;
		public $time;
		
		function __construct()
    {
    		$this->domain = $_SERVER['SERVER_NAME'];
    		$this->spider = get_spider_bot();
    		$this->time = strtotime(date('Y-m-d', time()));
    		if(!$this->spider)$this->spider='user';
    		$this->logfile = $this->getLogfile();
    		if(!file_exists($this->logfile)){
    				$this->TodayLog();
    				$this->spider_count();
    		}
    }
    
    function getSiteinfo(){
				$mlog = M('Todaylog');
				$info = $mlog -> where("create_time='".$this->time."'") -> find();
				return $info;
    }
		
		function writelog(){
				if(ACTION_NAME == 'verify' || $_GET['not_spider'])return false;
				$mlog = M('Viewlog');
				$ip = get_client_ip();
				$time = time();
				$modid = is_numeric(ACTION_NAME) ? ACTION_NAME : ($_POST['modid']?$_POST['modid']:0);
				$module = MODULE_NAME;
				if($_POST['module'])$module = $_POST['module'].'_'.$module;
				$log = array(
					'request_method' => $_SERVER['REQUEST_METHOD'],
					'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'spider_code' => $this->spider,
					'request_time' => $time,
					'module' => $module,
					'modid' => $modid,
					'ip' => $ip,
					'module_action' => is_numeric(ACTION_NAME) ? 'read' : ACTION_NAME,
					'request_uri' => $_SERVER['REQUEST_URI'],
					'server_protocol' => $_SERVER['SERVER_PROTOCOL']
				);
				$logkey = md5($log['module'].$log['modid'].$log['ip'].$log['module_action'].$log['request_method'].$log['spider_code']);
				if(cookie($logkey))return false;
				cookie($logkey, true);
				$id = $mlog->data($log)->add();
				$todaylog = array();
				$module = strtolower(MODULE_NAME);
				if(in_array($this->spider,$this->Spiders)){
						$todaylog['today_'.$this->spider] = array('exp', '(today_'.$this->spider.'+1)');
				}
				if(($this->spider=='user' || $this->spider=='baidu') && in_array($module, $this->modules)){
						$todaylog[$this->spider.'_'.$module] = array('exp', '('.$this->spider.'_'.$module.'+1)');
				}
				if($this->spider=='user'){
						$todaylog['total_user'] = array('exp', '(total_user+1)');
						if($modid && in_array($module, $this->modules)){
								$M = M($module);
								$M->where("id='".$modid."'")->setInc('views');
						}
				}else{
						$todaylog['total_spider'] = array('exp', '(total_spider+1)');
						if(in_array($module, $this->modules)){
								$todaylog['module_'.$module] = array('exp', '(module_'.$module.'+1)');
						}
				}
				if($todaylog){
						$MToday = M('Todaylog');
						$MToday -> data($todaylog) -> where("create_time='".$this->time."'") -> save();
				}
				return $id;
		}
		
    function TodayLog(){
    		$Logs = array();
    		$this->saveLog($Logs);
    		$mlog = M('Todaylog');
    		//$data = array('create_time' => $this->time,'today_baidu'=>0,'today_google'=>0,'today_sogou'=>0,'today_soso'=>0,'today_yahoo'=>0,'today_bing'=>0,'module_new'=>0,'module_product'=>0,'module_down'=>0,'module_case'=>0,'module_job'=>0,'module_blog'=>0,'baidu_new'=>0,'baidu_product'=>0,'baidu_down'=>0,'baidu_case'=>0,'baidu_job'=>0,'baidu_blog'=>0,'user_new'=>0,'user_product'=>0,'user_down'=>0,'user_case'=>0,'user_job'=>0,'user_blog'=>0,'site_baidu_count'=>0,'site_google_count'=>0,'site_sogou_count'=>0,'site_soso_count'=>0,'site_yahoo_count'=>0,'site_bing_count'=>0,'site_baidu_index'=>0,'site_google_index'=>0,'site_sogou_index'=>0,'site_soso_index'=>0,'site_yahoo_index'=>0,'site_bing_index'=>0,'site_baidu_plus'=>0,'site_google_plus'=>0,'site_sogou_plus'=>0,'site_soso_plus'=>0,'site_yahoo_plus'=>0,'site_bing_plus'=>0,'total_user'=>0,'total_spider'=>0,'total_count'=>0);
    		$data = array('create_time' => $this->time);
    		$mlog->data($data)->add();
    }
    
    function updatecount($model){
    		$module = strtolower(MODULE_NAME);
    		if(in_array($module, $this->modules)){
    				$Mod = M(MODULE_NAME);
    				$Category = M("Category");
    				if($model->catid){
	    				$count = $Mod->where("catid='".$model->catid."'")->count();
	    				$Category -> data(array('counts' => $count)) -> where("id='".$model->catid."'") -> save();
	    			}
	    			$count = $Mod->where("1")->count();
	    			$Category -> data(array('counts' => $count)) -> where("level=0 AND pid=0 AND module='".MODULE_NAME."'") -> save();
						$MToday = M('Todaylog');
						$MToday -> where("create_time='".$this->time."'") -> setInc('total_count'); 			
    		}
    }
    
    function ping($model){
    		
    }
    
    function saveLog($logs, $file){
    		if(!$file)$file = $this->logfile;
    		$content = "<?php\nreturn ".var_export(array_change_key_case($logs,CASE_LOWER),true).";\n?>";
				return file_put_contents($file,$content);    		
    }
    
    function getLogfile($time){
	    	if(!$time)$time=time();
	    	$logdir = LOG_PATH.'Spider'.date('Ym', $time).'/';
	    	if(!is_dir($logdir))mkdir($logdir);
	    	return $logdir.'spider_'.date('Y_m_d', $time).'.php';
    }
    
    function setDomain($domain){
    		$this->domain = $domain;
    }
    
    function setLinkdomain($domain){
    		$this->linkdomain = $domain;
    }    
    
    function spider_count(){
    		$lasttime = strtotime(date('Y-m-d', $this->time-600));
				$mlog = M('Todaylog');
				$this->LastLogs = $mlog -> where("create_time='".$lasttime."'") -> find();
    		foreach($this->Spiders as $val){
    			$count = 0;
    			$site = array('count'=>0,'index'=>0,'plus'=>0);
    			$method = $val.'_count';
    			if(method_exists($this,$method))$site = $this->$method();
    			$data['site_'.$val.'_count'] = $site['count'];
    			$data['site_'.$val.'_index'] = $site['index'];
    			$data['site_'.$val.'_plus'] = $site['plus'];
    		}
    		$data['baidu_br'] = getbr($this->domain);
    		$data['google_pr'] = getpr($this->domain);
    		$mlog -> where("create_time='".$this->time."'") -> data($data) -> save();
    }
    
    function baidu_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('baidu');
	    	$html = GetContent($url);
	    	$count1 = preg_replace('/[^\d]/', '', GetField($html, '<p class="site_tip"><strong>找到相关结果数', '个。</strong>'));
	    	$count2 = preg_replace('/[^\d]/', '', GetField($html, '<span class="nums"  style="margin-left:120px" >百度为您找到相关结果约', '个</span>'));
	    	$domainstr = trim(GetField($html, '<span class="g">', '</span>'));
	    	$temp = explode(' ', $domainstr);
	    	$domain = $temp[0];
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval(max($count1, $count2));
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_baidu_count']);
				if($count>0 && $domain!=$this->domain)$site['index']=1;
				if($site['index']==0 && $count > 0)$site['date'] = strtotime($temp[1]);
	    	return $site;
    }
    
    function google_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('google');
	    	$html = GetContent($url);
	    	$count = preg_replace('/[^\d]/', '', GetField($html, '<div id="resultStats">&#32004;&#26377;', '&#38917;&#32080;&#26524;</div>'));
	    	$domain = GetField($html, '<div class="s"><div class="kv" style="margin-bottom:2px"><cite>', '</cite>');
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval($count);
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_google_count']);
	    	if($count>0 && $domain!=$this->domain)$site['index']=1;
	    	return $site;
    }
    
    function sogou_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('sogou');
	    	$html = GetContent($url);
	    	$count = preg_replace('/[^\d]/', '', GetField($html, '<span id="scd_num">', '</span>'));
	    	$domain = GetField($html, '<cite id="cacheresult_info_(*) - <b>', '</b>');
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval($count);
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_sogou_count']);
	    	if($count>0 && $domain!=$this->domain)$site['index']=1;
	    	return $site;
    }
    
    function soso_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('soso');
	    	$html = GetContent($url);
	    	$html = ConvertChatSet($html,'gb2312','utf-8');
	    	$count = preg_replace('/[^\d]/', '', GetField($html, '<div id="sInfo">搜索到约', '项结果(*)</div>'));
	    	$domain = GetField($html, '<div class="url"><cite>', '&nbsp;(*) -</cite>');
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval($count);
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_soso_count']);
	    	if($count>0 && $domain!=$this->domain)$site['index']=1;
	    	return $site;
    }
    
    function yahoo_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('yahoo');
	    	$html = GetContent($url);
	    	$count = preg_replace('/[^\d]/', '', GetField($html, '<div class="s_info">找到相关网页约', '条</div>'));
	    	$domain = GetField($html, '<span class="url">', '</span>');
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval($count);
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_yahoo_count']);
	    	if($count>0 && $domain!=$this->domain)$site['index']=1;
	    	return $site;
    }
    
    function bing_count(){
	    	$site = array('count'=>100,'index'=>0,'plus'=>0);
	    	$url = $this->getDomainSite('bing');
	    	$html = GetContent($url);
	    	$count = preg_replace('/[^\d]/', '', GetField($html, '<div class="sb_rc_btm sb_rc_btm_p">', '条结果</div>'));
	    	$domain = GetField($html, '<div class="sb_meta"><cite>', '</cite>');
	    	$domain = preg_replace('/<\/?strong>/i', '', $domain);
	    	if(substr($domain, -1)=='/')$domain = substr($domain,0,-1);
	    	$count = intval($count);
	    	$site['count'] = $count;
	    	$site['plus'] = $count - intval($this->LastLogs['site_bing_count']);
	    	if($count>0 && $domain!=$this->domain)$site['index']=1;
	    	return $site;
    }
    
    function checkLink($url){
    		if(!$url)$url='http://'.$this->domain.'/';
    		$link['linktype'] = 3;
    		$html = GetContent($url);
    		if(!$html)return $link;
    		preg_match_all('/'.$this->linkdomain.'/i', $html, $match);
    		if(!$match[0]){
    			$link = array(
    				'linkurl' => '',
    				'linktype' => 4,
    				'linktext' => ''
    			);
    			return $link;
    		}
				$reg='/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';
				preg_match_all($reg, $html, $links);
				foreach($links[2] as $key => $val){
						preg_match_all('/'.$this->linkdomain.'/i', $val, $match);
						if($match[0]){
								preg_match_all('/<img/i', $links[4][$key], $match);
								$link['linkurl'] = $val;
								$link['linktype'] = $match[0] ? 2 : 1;
								$link['linktext'] = $link['linktype']==1 ? $links[4][$key] : '';
								break;
						}
				}
				return $link;
    }
    
    function getDomainSite($s){
	    	switch($s){
	    		case 'baidu':
	    			$url = 'http://www.baidu.com/s?wd=site:'.$this->domain.'&ie=utf-8';
	    			break;
	    		case 'google':
	    			$url = 'http://www.google.com.hk/search?hl=utf-8&newwindow=1&safe=strict&tbo=d&site=&source=hp&q=site:'.$this->domain;
	    			break;
	    		case 'sogou':
	    			$url = 'http://www.sogou.com/web?query=site:'.$this->domain;
	    			break;
	    		case 'soso':
	    			$url = 'http://www.soso.com/q?w=site:'.$this->domain;
	    			break;
	    		case 'yahoo':
	    			$url = 'http://search.cn.yahoo.com/s?q=site:'.$this->domain;
	    			break;
	    		case 'bing':
	    			$url = 'http://cn.bing.com/search?q=site:'.$this->domain;
	    			break;
	    	}
	    	return $url;
    }

}
?>

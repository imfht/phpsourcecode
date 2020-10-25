<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class LinkAction extends CommonAction {
	
		public function index()
	  {
	      //列表过滤器，生成查询Map对象
	      C('PER_PAGE', 100);
	      $map = $this->_search();
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
				$model = M($this->getActionName());
	      if(!empty($model)) {
	        	$this->_list($model,$map,'linktype DESC, baidu_index DESC, update_time DESC, sort',true);
	      }
				$this->display();
	      return;
	  }
    
    public function linkcheck(){
    		if(!$_GET['ischeck'])exit('end');
    		$lockfile = TEMP_PATH.'link.lock';
    		if(file_exists($lockfile) && time()-filemtime($lockfile) < 300)exit;
        import('ORG.Util.Spider');
        $domain = $_SERVER['SERVER_NAME'];
        $spider = new Spider();
        $LinkModel = M('Link');
        $time = strtotime(date('Y-m-d'));
        $linkfile = $this->getLinkfile();
        if(!file_exists($linkfile)){
	        	$links = $LinkModel->where("update_time<'".$time."' AND status=1")->select();
	        	$this->saveLink($links, $linkfile);
	      }else{
	      		$links = include($linkfile);
	      }
        if(!$links)exit('end');
        $this->saveLink('', $lockfile);
	      foreach($links as $key => $val){
		        $link = $val;
		        $linkid = $key;
		        break;
	      }
		    $url = parse_url($link['url']);
		    if(!$url['path'])$url['path'] = '/';
		    $spider->setDomain($url['host']);
		    $site = $spider->baidu_count();
		    $spider->setLinkdomain($domain);
		    $info = $spider->checkLink('http://'.$url['host'].$url['path']);
		    $info['baidu_count'] = $site['count'];
		    $info['baidu_index'] = $site['index'];
		    $info['baidu_date'] = $site['date'];
		    $info['update_time'] = time();
		    $info['update_date'] = $time;
		    $pr = getpr($url['host']);
		    $br = getbr($url['host']);
		    $info['google_pr'] = intval($pr);
		    $info['baidu_br'] = intval($br);
		    $LinkModel->data($info)->where("id='".$link['id']."'")->save();
		    if($info['linktype']<=2){
		    		$outhtml .= '<li class="link"><a href="'.$link['url'].'" target="_blank"><i class="icon-ok icon-white"></i>'.$link['title'].'<span class="status_available"></span></a></li>';
		    }elseif($info['linktype']==4){
		    		$outhtml .= '<li><a href="'.$link['url'].'" target="_blank"><i class="icon-remove icon-white"></i>'.$link['title'].'<span class="status_off"></span></a></li>';
		    }elseif($info['linktype']==3){
		    		$outhtml .= '<li><a href="'.$link['url'].'" target="_blank"><i class="icon-question-sign icon-white"></i>'.$link['title'].'<span class="status_away"></span></a></li>';
		    }
		    $linktype = $info['linktype'];
		    if($info['baidu_index'] && $linktype < 3)$linktype = 10;
		    if(in_array($linktype, C('LINK_INDEX_REMOVE')))$data['isindex'] = 0;
		    if(in_array($linktype, C('LINK_REMOVE')))$data['status'] = 0;
		    if($data){
		    		$LinkModel->data($data)->where("id='".$linkid."'")->save();
		    }
        S('Default_links', null);
        unset($links[$linkid]);
        $this->saveLink($links, $linkfile);
        unlink($lockfile);
        echo $outhtml;
    }
    
    function saveLink($links, $file){
    		if(!$file)$file = $this->getLinkfile();
    		if(is_array($links)){
    				$content = "<?php\nreturn ".var_export(array_change_key_case($links,CASE_LOWER),true).";\n?>";
    		}else{
    				$content = $links;
    		}
				return file_put_contents($file,$content);    		
    }
    
    function getLinkfile($time){
	    	if(!$time)$time=time();
	    	$logdir = LOG_PATH.'Link'.date('Ym', $time).'/';
	    	if(!is_dir($logdir))mkdir($logdir);
	    	return $logdir.'link_'.date('Y_m_d', $time).'.php';
    }    
		
}
?>
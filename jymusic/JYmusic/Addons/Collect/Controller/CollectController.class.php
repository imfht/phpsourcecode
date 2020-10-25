<?php
/**

 */
namespace Addons\Collect\Controller;
use Common\Controller\Addon;
use Home\Controller\AddonsController;


class CollectController extends AddonsController {
	public function add() {
		$server = S('song_server');
		if (!$server){
			$server = M ( "Server" )->field('id,name,url,listen_dir,down_dir')->select ();
			S('song_server',$server);
		}	
		$this->meta_title ="新增规则";
		$this->server = $server;
		$this->display ( T('Addons://Collect@Collect/add'));
	}
	
	public function mod() {
		$id = (int)I('get.id');
		if (!$id) $this->error('参数错误，采集规则不存在');
		$server = S('song_server');
		if (!$server){
			$server = M ("Server")->field('id,name,url,listen_dir,down_dir')->select ();
			S('song_server',$server);
		}
		$detail = D('Addons://Collect/Collect')->detail($id);
		$this->meta_title ="编辑规则";
		$this->server = $server;
		$this->rule = $detail;
		$this->display ( T('Addons://Collect@Collect/add'));
	}
	
	/* 删除规则 */
	public function del(){
		$id     =   I('get.id','');
		if(D('Addons://Collect/Collect')->del($id)){
			$this->success('删除成功', U('/addons/adminlist',array('name'=>'Collect')));
		}else{
			$this->error(D('Addons://Collect/CollectRule')->getError());
		}
	}
	
	/* 更新规则 */
	public function update(){
		
		$res = D('Addons://Collect/Collect')->update();
		if(!$res){
			$this->error(D('Addons://Collect/Collect')->getError());
		}else{
			if($res['id']){
				$cacheNmae  = 'rlue_'.$res['id'];
				F($cacheNmae,NULL);
				$this->success('更新成功', U('/addons/adminlist',array('name'=>'Collect')));
			}else{
				$this->success('新增成功', U('/addons/adminlist',array('name'=>'Collect')));
			}
		}
	}
	
	
	// 执行采集
	public function start (){		
		if ( IS_POST){
			$id = (int)I('post.id');
			$cacheNmae  = 'rlue_'.$id;
			$rlue = F($cacheNmae);			
			if(!$rlue){ //没有缓存 初次采集初始化数据					
				$detail = D('Addons://Collect/Collect')->detail($id);
				if (!$detail) $this->error('参数错误，采集规则不存在');
				//分解成变量
				extract($detail);
				//规则转数组
				$data['rulename'] = $rule_name;
				$data['first_page_id']  = $first_page_id;
				$data['last_page_id']  = $last_page_id;
				$data['page_rule'] = $page_rule;	
				$data['link_wrap_rule'] = explode("@",$link_wrap_rule);
				$data['link_rule'] = explode("@",$link_rule);
				$data['title_rule'] = explode("@",$title_rule);
				$data['play_rule'] = explode("@",$play_rule);
				$data['server_id'] = $server_id;
				if (!empty($play_rule2)) $data['play_rule2'] = explode("@",$play_rule2);
				
				//获取网站地址
				$tempu=parse_url($page_rule);  
				$url= 'http://' . $tempu['host'] . '/';
				$data['url']=$url;
				$server = M('Server')->find($server_id);
				if ($server){
					$data['server'] = $server['url'] . $server['listen_dir'];
				}
				F($cacheNmae , $data);	
				$retrn['rulename'] = $rule_name;
				$retrn['p'] = $first_page_id;				
			}else{
				$retrn['rulename'] = $rlue['rulename'];
				$retrn['p'] = $rlue['first_page_id'];				
			}
			$retrn['status'] = 1;
			$this->ajaxReturn($retrn);			
		}else{
			$id = (int)I('get.id');
			$cacheName = 'collect_music_list_'.$id;
			$musicList = F($cacheName );
			krsort($musicList);
			if ($musicList){
				$this->musicList = $musicList;
				$this->listCount = count($musicList);
				$this->meta_title ="音乐入库";	
				$this->display ( T('Addons://Collect@Collect/list'));				
			}else{		
				$this->meta_title ="采集音乐";			
				$this->display ( T('Addons://Collect@Collect/start'));
			}				
		}
	}
	
	
	
	
	public function  getlist (){
		$id = (int)I('post.id');
		$p = (int)I('post.p');
		$listid = (int)I('post.listid');
		$listid  = !$listid? 0 : $listid ;
		$cacheNmae  = 'rlue_'.$id;
		$rlue = F($cacheNmae);
		//查询分页列表音乐缓存
		$pagelinks = F($cacheNmae.'_'.$p); 
		if (!$pagelinks){//没有缓存
			$url = str_replace('{$id}', $p, $rlue['page_rule']);	
			$weburl = parse_url($url);  
			$weburl="http://" . $weburl['host'];		
			$results = $this->create_html($url);
			
			
			//匹配  链接容器
			$preg = $this->get_preg($rlue['link_wrap_rule']);		
					
			preg_match($preg, $results['content'], $box);
			//echo $results['content'];
							
			//匹配  链接
			$preg = $this->get_preg($rlue['link_rule']);
			preg_match_all($preg, $box[1], $links);
			if ($links[1]){
				foreach ( $links[1] as &$v ){
					if(substr($v,0,7) !="http://") $v=$weburl.$v;			
				}
			}
			$pagelinks =$links[1];
			session('charset',$results['charset']);//缓存编码
			session('linksCount',count($pagelinks));
			F($cacheNmae.'_'.$p,$pagelinks);//缓存获取到的当前分页的链接列表		
		}
		$count = session('linksCount');
		$charset = session('charset');
		if ($count > 0){
			//获取单页地址
			$playPageUrl = $pagelinks[$listid];
			
			//采集单页
			$results = $this->create_html($playPageUrl,$charset);
			
			//获取标题
			$titlepreg = $this->get_preg($rlue['title_rule']);
			preg_match($titlepreg, $results['content'], $title);
									
			//获取播放地址
			$playpreg = $this->get_preg($rlue['play_rule']);
			preg_match($playpreg, $results['content'], $play);	
			if (!empty($rlue['play_rule2'])){
				$playpreg = $this->get_preg($rlue['play_rule2']);
				preg_match($playpreg, $results['content'], $play2);
				$play[1] =  $play[1].$play2[1];	
				if (strstr($play[1], '\u')){
					$play[1] = $this->unicode_decode($play[1]);					
				}
			}
			/*$playpreg = $this->get_preg($rlue['play_rule']);
			preg_match($playpreg, $results['content'], $play);*/	
			//缓存音乐数据
			if ($title[1] && $play[1] ){
				$musicList = F('collect_music_list_'.$id);
				$return['title'] = $title[1];
				$return['playUrl'] = $play[1];
				$musicList[] = $return;					
				F('collect_music_list_'.$id,$musicList);
				$return['status'] = 1;					
			}else{
				$return['status'] = 2;
			}
		}
		$lastp = intval($rlue['last_page_id']);
		if ($count < 1 && $lastp <= $p ) {//采集完成
			F($cacheNmae.'_'.$p,NULL);
			F($cacheNmae.'_'.$p,NULL);
			$return['status'] = 0;						
		}elseif ($count < 1){
			F($cacheNmae.'_'.$p,NULL); 
			$return['p'] = ++$p;
			$return['listid']=0;
			$return['status'] = 1;			
		}else{
			$return['p'] = $p;
			$return['listid'] = ++$listid;	
		}
			
		$count = --$count;
		session('linksCount',$count);
		$this->ajaxReturn($return);	
	}
	
	
	//入库 
	public function  import (){	
		$rule_id = (int)I('get.rule_id');
		$start = (int)I('get.start');	
    	if(IS_POST ){ //初始化
    		$data = I('post.');
    		$data['status'] = '1';			
			$data['up_uname'] = get_nickname($data['up_uid']);
			$data['genre_name'] = get_genre_name($data['genre_id']);
			$cacheNmae  = 'rlue_'.$data['rule_id'];
			$rlue = F($cacheNmae);
			$data['server'] = $rlue['server'];
			$data['server_id'] = $rlue['server_id'];
			$count = count(F('collect_music_list_'.$data['rule_id']));
			//这里给出的是总数量  后续索引得减一
			$tab = array('rule_id'=>$data['rule_id'],'start' => $count); 						
    		session('post_storage_data',$data);
    		 //检查是否有正在执行的任务   realpath
            $lock = "./Uploads/Music/storage.lock";            
            if(is_file($lock)){
                $this->error('检测到有一个导入任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, NOW_TIME);
            }
            $this->success('初始化成功！', '', array('tab' => $tab));
    	}elseif (IS_GET && is_numeric($rule_id) && is_numeric($start)) { //导入数据  			
			if($start){
				$listkey = ($start-1);
				$cacheName = 'collect_music_list_'.$rule_id;
				$musicList = F($cacheName );
				$music =$musicList[$listkey];	
				$data = session('post_storage_data');
				$data['add_time'] = $data['update_time']  = NOW_TIME;
				$data['name'] = $music['title'];//获取名称					
				$data['music_url'] = $data['music_down'] = $data['server'] . $music['playUrl'];		
				$data['server'] = $data['server_id'];
				$Songs = M('Songs');
				$data = $Songs->create($data);				
				$map['uid'] =$data['up_uid'];	
				if($Songs->add($data)){			
					M("Member")->where($map)->setInc('songs',1);//增加上传歌曲数量					
					if ($start > 1){
						unset($musicList[$listkey]);						
						F($cacheName,$musicList);						
					}else{
						F($cacheName,null);
					}										
                	$tab = array('rule_id'=>$rule_id,'start' => --$start);
                	$this->success('入库完成！', '', array('tab' => $tab));
					exit;
                }else{
                	$this->error('导入失败！');
                }
            } else { //清空缓存
  				unlink('./Uploads/Music/storage.lock');  //删除锁文件     
            	session('post_storage_data',null);         	
                $this->success('入库完成',1);
            }    			
    	
    	}else { //出错
            $this->error('参数错误！');
        }
		
	}
	
	//清除采集缓存
	public function  clearCache (){
		$id = I('post.id');
		$cacheName = 'collect_music_list_'.$id;
		$status = F($cacheName,null);
		if ($status){
			$this->success('成功采清除集缓存',1);
		}else{
			$this->error('采集缓存清除失败，请手动删除/Runtime/Data 文件夹下所有文件后！ 刷新页面');			
		}
		
	}
		
	public function  test (){

				
	}
		
		
	//获取htnl 转码 替换空白和换行
	protected function create_html ($url,$charset=""){ 
		$snoopy = new Snoopy ();
		$snoopy->fetch($url);		
		$request = $snoopy->results;
		$headers = $snoopy->headers;
		
		if (empty($charset)) $charset=$this->getCharset($request,$headers);
		
		if($charset!="utf-8"){
			$request=mb_convert_encoding($request,"UTF-8",$charset);			 
		}		
		//$content= iconv("gb2312","utf-8//IGNORE",$str);
		$find           = array('~>\s+<~','~>(\s+\n|\r)~');
		$replace        = array('><','>');	
		$request = str_replace("\r\n", '', $request); //清除换行符
		$request = str_replace("\n", '', $request); //清除换行符
		$request = str_replace("\t", '', $request); //清除制表符 
 		
        $return['content']  = preg_replace($find, $replace, $request);		
		//$return['content'] = $request;
		$return['charset']  = $charset;
		return  $return;
	}

	//生成正则
	protected function get_preg($arr){  
 		$startstr=str_replace("'","\'",str_replace("/","\/",$arr[0]));
	    $endstr=str_replace("'","\'",str_replace("/","\/",$arr[1]));
	    $preg='/'.$startstr.'(.*?)'.$endstr.'/';
		return $preg;  
	}
	
	
	//获取网站编码
    public function getCharset($content,$headers){
        if(!$content){return false; exit;}
        //首先从html获取编码
		$charset_arr=array(
			'gb2312','utf-8','big5','gbk','ascii','euc-jp','shift_jis','cp936','iso-8859-1','ibm037','jis','eucjp-win','sjis-win'
		);
        preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$content,$temp) ? strtolower($temp[1]):"";	
        if($temp[1]!=""){
			return $temp[1];
        }
              
        if(!empty($headers)){
            //从header中获取编码
            $hstr=strtolower(implode("|||",$headers));
            preg_match("/charset=[^\w]?([-\w]+)/is",$hstr,$lang) ? strtolower($lang[1]):"";
            if($lang[1]!=""){
                return $lang[1];
            }
        }
         
        $encode_arr=array("UTF-8","GB2312","GBK","BIG5","ASCII","EUC-JP","Shift_JIS","CP936","ISO-8859-1","JIS","eucjp-win","sjis-win");
        $encoded=mb_detect_encoding($results,$encode_arr);
        if($encoded){
            return strtolower($encoded);
        }else{
            return false;
        }
    }
					
	/**
	 * $str 原始中文字符串
	 * $encoding 原始字符串的编码，默认GBK
	 * $prefix 编码后的前缀，默认"&#"
	 * $postfix 编码后的后缀，默认";"
	 */
	function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
		$str = iconv($encoding, 'UCS-2', $str);
		$arrstr = str_split($str, 2);
		$unistr = '';
		for($i = 0, $len = count($arrstr); $i < $len; $i++) {
			$dec = hexdec(bin2hex($arrstr[$i]));
			$unistr .= $prefix . $dec . $postfix;
		} 
		return $unistr;
	} 
	 
	// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
	function unicode_decode($name){
		
		$pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
		preg_match_all($pattern, $name, $matches);
		if (!empty($matches)){
			$name = '';
			for ($j = 0; $j < count($matches[0]); $j++)
			{
				$str = $matches[0][$j];
				if (strpos($str, '\\u') === 0)
				{
					$code = base_convert(substr($str, 2, 2), 16, 10);
					$code2 = base_convert(substr($str, 4), 16, 10);
					$c = chr($code).chr($code2);
					$c = iconv('UCS-2', 'UTF-8', $c);
					$name .= $c;
				}
				else
				{
					$name .= $str;
				}
			}
		}
		return $name;
	}
	
	function zm (){
		$string = str_replace("\r\n", '', $string); //清除换行符
		$string = str_replace("\n", '', $string); //清除换行符
		$string = str_replace("\t", '', $string); //清除制表符   
		return preg_replace($pattern, $replace, $string);
		
		
	}
	
	

	
}

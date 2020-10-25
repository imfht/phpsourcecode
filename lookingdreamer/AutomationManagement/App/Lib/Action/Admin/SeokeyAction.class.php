<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class SeokeyAction extends CommonAction {
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
     
    public function _before_insert() {
        $this->checkSeokey();
    }
    
    public function _before_update() {
        $this->checkSeokey();
    }
    
		public function _tigger_insert($model) {

		}

		public function _tigger_update($model) {
				$this->updateModule($model->id);
		}
		
		public function seoword(){
				if($_GET['wid']){
					$list = $this->getSeowords();
					$html = '';
					if($list){
						foreach($list as $val){
							$html .= '<tr class="row"><td><input type="checkbox" class="style checkboxrow" value="'.$val['id'].'"></td><td>'.$val['title'].'</td><td>'.$val['wd'].'</td><td>'.$val['dp'].'</td><td></td></tr>';
						}
						echo $html;
					}else{
						echo "failed";
					}
					echo $html;
				}else{
					echo "failed";
				}
		}
		
		public function search(){
				$cachefile = DATA_PATH.'~seowords.php';
				if(file_exists($cachefile))$swlist = include($cachefile);	
				$Mseowords = M('seowords');
				C('PER_PAGE',50);
				if($_POST['wd']){
					$wid = md5($_POST['wd']);
					if(!$swlist[$wid]){
							$swlist[$wid] = $_POST['wd'];
							$content = "<?php\nreturn ".var_export($swlist,true).";\n?>";
							$iscache = file_put_contents($cachefile,$content);						
					}
					if($_GET['start']){
							$Mseowords -> where("wid='".$wid."'") -> delete();					
					}else{
							$map = "wid='".$wid."' AND dp>0";
				      if(!empty($Mseowords)) {
				        	$this->_list($Mseowords,$map,'id',true);
				      }
					}
					$this->assign("wid",$wid);
					$seoword = $Mseowords -> where("wid='".$wid."' AND dp='0'") -> order("id ASC") -> find();
					if(!$seoword){
						$sw = array(
							'title' => $_POST['wd'],
							'wd' => $_POST['wd'],
							'bs' => '',
							'dp' => 0,
							'create_time' => time(),
							'update_time' => 0,
							'status' => 0,
							'wid' => $wid,
						);
						$Mseowords -> data($sw) -> add();					
					}
				}else{
					$map = "dp>0";
				  if(!empty($Mseowords)) {
				    	$this->_list($Mseowords,$map,'id',true);
				  }
				}
				$this->assign("swlist",$swlist);
				$this->display();
		}
		
		public function getSeowords(){
				set_time_limit(0);
				$list = array();
				$wid = $_GET['wid'];
				$dp = intval($_GET['dp']);
				if(!$dp)$dp = 2;
				if(!$wid)$wid = md5($_GET['wd']);
				$Mseowords = M('Seowords');
				$cachefile = DATA_PATH.'~seowords.php';
				if(file_exists($cachefile)){
					$swlist = include($cachefile);
					$seoword = $Mseowords -> where("wid='".$wid."' AND update_time=0 AND status=0") -> order("id ASC") -> find();
					if($seoword){
						if($seoword['dp']>=$dp)return false;
						$dp = $seoword['dp'] + 1;
						$wd = $seoword['title'];
						$bs = $seoword['bs'];
					}else{
						return false;
					}
				}else{
					return false;
				}
				
				$url = getBaiduUrl($wd, $bs);
				$html = GetContent($url);
				$html = GetField($html, '<div id="rs">', '</div>', 0);
				$seowords = GetField($html, '<a href="/s?wd=(*)">', '</a>', 10);
				foreach($seowords as $key => $val){
					if(!$val)continue;
					$sid = md5($val);
					if(!$seowordlist[$sid]){
						$seowordlist[$sid] = $val;
					}
				}
				$url = getBaiduUrl($wd, $bs,2);
				$html = GetContent($url);
				$html = GetField($html, 'window.bdsug.sug({q:"(*)",p:false,s:["', '"]});', 0);
				$seowords = explode('","', $html);
				foreach($seowords as $key => $val){
					if(!$val)continue;
					$sid = md5($val);
					if(!$seowordlist[$sid]){
						$seowordlist[$sid] = $val;
					}
				}
				$word = ConvertChatSet($wd);
				$url = getBaiduUrl($word,'',3);
				$html = GetContent($url);
				$html = ConvertChatSet($html,'gb2312','utf-8');
				$html = GetField($html, '<div class="itemarea l">', '<!-- 地区分布 -->', 0);
				$seowords = GetField($html, '<td><a href="./word.php(*)">', '</a></td>', 20);
				foreach($seowords as $key => $val){
					if(!$val)continue;
					$sid = md5($val);
					if(!$seowordlist[$sid]){
						$seowordlist[$sid] = $val;
					}
				}
				$templist = $Mseowords -> where("title IN('".implode("','", $seowordlist)."')") -> select();
				if($templist){
					foreach($templist as $val){
						$sid = md5($val['title']);
						unset($seowordlist[$sid]);
					}
				}
				$list = array();
				foreach($seowordlist as $key => $val){
					$sw = array(
						'title' => $val,
						'wd' => $swlist[$wid],
						'bs' => $wd,
						'dp' => $dp,
						'create_time' => time(),
						'update_time' => 0,
						'status' => 0,
						'wid' => $wid,
					);
					$sw['id'] = $Mseowords -> data($sw) -> add();
					$list[] = $sw;
				}
				if($seoword['id']){
					$Mseowords -> where("id='".$seoword['id']."'") -> data(array('update_time'=>time(),'status'=>1)) -> save();
				}
				return $list;
		}
     
    public function sort()
    {
				$Manual = M('Seokey');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
        }else{
            $map['status'] = 1;
        }
        $sortList   =   $Manual->where($map)->order('sort asc')->select();
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
    
    public function checkSeokey()
    {
    		$id = intval($_POST["id"]);
    		$title = trim($_POST["title"]);
    		$SeoModel = M("Seokey");
    		$seo = $SeoModel->where("title='".$title."' AND id<>'".$id."'")->find();
    		if($seo)$this->error(L('长尾关键词已经存在，不能重复！'));
    }
    
    public function updateModule($id)
    {
    	if(!empty($id)){
    		$SeoModel = M("Seokey");
    		$seo = $SeoModel->find($id);
    		if(!$seo)return false;
    		$module = $seo["module"];
    		$modid = $seo["modid"];
    		if($module && $modid){
	    		$model = M($module);
	    		$Pk = $model->getPk();
	    		$data = array(
	    			'seokey' => $seo['title'],
	    			'is_title_in_url' => $seo['is_title_in_url'],
	    			'is_title_to_pinyin' => $seo['is_title_to_pinyin'],
	    			'urlwords' => $seo['urlwords']
	    		);
	    		$model->where($Pk."='".$modid."'")->data($data)->save();
	    	}
    	}
    }
    
	// 缓存文件
	public function cache($name='',$fields='') {
		$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M($name);
		$list		=	$Model->where("status=1")->select();
		$data		=	array();
		foreach ($list as $key=>$val){
			if($val['module'] && $val['modid'])$val['url'] = getReadUrl($val["modid"], $val, $val['module'], 1);
			$temp = array(
				'Key' => $val['title'],
				'Href' => '<a href="'.$val['url'].'" target="_blank">'.$val['title'].'</a>',
				'id' => intval($val['id']),
				'ReplaceNumber' => intval($val['times'])
			);
			$data[$val['id']] = $temp;
		}
		if(C('SEOKEY')){
			$seokeys = explode(',', C('SEOKEY'));
			$url = 'http://'.$_SERVER['SERVER_NAME'].'/';
			foreach($seokeys as $val){
				if($val){
					$temp = array(
						'Key' => $val,
						'Href' => '<a href="'.$url.'" target="_blank">'.$val.'</a>',
						'ReplaceNumber' => 1,
						'id' => 0
					);
					$data[md5($val)] = $temp;
				}
			}
		}
		$savefile	=	DATA_PATH.'~seokey.php';
		// 所有参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($data,CASE_UPPER),true).";\n?>";
		$iscache = file_put_contents($savefile,$content);	
		if($iscache){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
}
?>
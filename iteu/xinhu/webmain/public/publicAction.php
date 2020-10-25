<?php 
class publicClassAction extends ActionNot{
	
	public function initAction()
	{
		$this->mweblogin(0, false);
	}
	
	//文档预览的
	public function fileviewerAction()
	{
		$id = (int)$this->get('id','0');
		$fobj = m('file');
		$frs= $fobj->getone($id);
		if(!$frs)exit('文件的记录不存在了1');
		$type 		= $frs['fileext'];
		$filepath 	= $frs['filepath'];
		
		if(substr($filepath, 0,4)!='http' && !file_exists($filepath))exit('文件不存在了2');
		
		$types 		= ','.$type.',';
		//可读取文件预览的扩展名
		$docx	= ',doc,docx,xls,xlsx,ppt,pptx,';
		if(contain($docx, $types)){
			$filepath 	= $frs['pdfpath'];
			if(isempt($filepath)){
				$this->topdfshow($frs, 1);
				return;
			}
			if(!file_exists($filepath)){
				$this->topdfshow($frs, 1);
				return;
			}else{
				$exta = substr($filepath, -4);
				if($exta=='html')$this->rock->location($filepath);
			}
		}else if($type=='mp4'){
			$this->displayfile = ''.P.'/public/fileopen_mp4.html';		
		}else if($fobj->isyulan($type)){
			$content  = file_get_contents($filepath);
			if(substr($filepath,-6)=='uptemp')$content = base64_decode($content);
			$bm =  c('check')->getencode($content);
			if(!contain($bm, 'utf')){
				$content = @iconv($bm,'utf-8', $content);
			}
			$this->smartydata['content'] = $content;
			$this->displayfile = ''.P.'/public/fileopen.html';
		}else if($type=='pdf'){
		
		}else{
			$this->topdfshow($frs,0);
			return;
			//exit('文件类型为['.$type.']，不支持在线预览');
		}
		$str = 'mode/pdfjs/web/viewer.css';
		if(!file_exists($str))exit('未安装预览pdf插件，不能预览该文件，可到信呼官网下查看安装方法，<a target="_blank" href="'.URLY.'view_topdf.html">查看帮助?</a>。');
		$this->smartydata['filepath'] = $this->jm->base64encode($filepath);
		$this->smartydata['filepaths']= $filepath;
		$this->smartydata['filename'] = $frs['filename'];
		$fobj->addlogs($id,0);//记录预览记录
	}
	
	private function topdfshow($frs, $lx=0)
	{
		if($lx==1){
			$officeyl	= getconfig('officeyl','0');
			if($officeyl=='2' || $officeyl=='3'){//用微软文档服务
				$filepath = $frs['filepath'];
				if(substr($filepath, 0,4)!='http'){
					$filepath = ''.getconfig('outurl',URL).''.$filepath.'';
				}
				$url = 'https://view.officeapps.live.com/op/view.aspx?src='.urlencode($filepath).'';
				if($officeyl=='3')$url = 'https://docview.mingdao.com/op/view.aspx?src='.urlencode($filepath).'';
				$this->rock->location($url);
				return;
			}
			if($officeyl=='5'){
				$url = 'index.php?a=fileedit&m=public&id='.$frs['id'].'&otype=1';
				$this->rock->location($url);
				return;
			}
		}
		$this->displayfile = ''.P.'/public/filetopdf.html';
		$this->smartydata['frs'] = $frs;
		$this->smartydata['ismobile'] = $this->rock->ismobile()?'1':'0';
	}
	
	/**
	*	请求转化
	*/
	public function changetopdfAjax()
	{
		$id = (int)$this->get('id','0');
		return c('xinhuapi')->officesend($id);
	}
	
	/**
	*	获取状态
	*/
	public function officestatusAjax()
	{
		$id = (int)$this->get('id','0');
		return c('xinhuapi')->officestatus($id);
	}
	
	/**
	*	获取状态
	*/
	public function officedownAjax()
	{
		$id = (int)$this->get('id','0');
		return c('xinhuapi')->officedown($id);
	}
	
	/**
	*	第三方编辑调用
	*/
	public function fileeditAction()
	{
		$id = (int)$this->get('id','0');
		$otype = (int)$this->get('otype','0');
		$this->smartydata['id'] = $id;
		$this->smartydata['otype'] = $otype;
	}
}
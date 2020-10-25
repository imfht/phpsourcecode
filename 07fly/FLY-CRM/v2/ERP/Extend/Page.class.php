<?php
 	//路径使用的分隔号
 /**
 +------------------------------------------------------------------------------
 * Framk 
 +------------------------------------------------------------------------------
 * @package  Lib
 * @author   hifan <helofan@163.com>
 * @version  0.1
 +------------------------------------------------------------------------------
 */

class Page{
	/////////////////////////////////

	private $pageSize;
	private $currentPage;
	private $totalPage;
	private $subUrl;
	private $totalRecord;
	private $htmlPage;
	private $ext='';//后缀，在用伪静态时在页数后加，.html


	public function __construct($params){
		$this->totalRecord=$params[0];	//总记录数
		$this->pageSize=$params[1];	//一页显示的记录条数
		$this->currentPage=$params[2];	//当前页	
		$this->subUrl=$params[3];	//	访问路径
	    (count($params)>4)?$this->ext=$params[4]:$this->ext= '';//如果参数有5个则应为后缀
		$this->createPage();

	}
	
	public function createPage(){

		if($this->totalRecord==0){
			$this->htmlPage="";//总记录为零则不输出
		}else{
		///////////////////	 //////////////////////
			if(empty($this->currentPage)==true || is_numeric($this->currentPage)==false){
				$this->currentPage=1;
			}else{
				$this->currentPage=intval($this->currentPage);
			}
			
			if($this->totalRecord<$this->pageSize){
				$this->totalPage=1;
			}else{
				if($this->totalRecord%$this->pageSize==0){
					$this->totalPage=intval($this->totalRecord/$this->pageSize);
				}else{
					$this->totalPage=intval($this->totalRecord/$this->pageSize)+1;
				}			
			}
			if($GLOBALS['ReWrite']){
				$this->htmlPage=$this->showPage_static();
			}else{
				$this->htmlPage=$this->showPage_static();
			}
			
		
		}//else
		return $this->htmlPage;
	
	}
/////////////
	public function showPage_static(){
	
		$pre=1;
		$next=$this->currentPage+1;
		$path=$this->subUrl;
		$var="'";
		$firstPage="1";		
		$begin=1;
		$end=6;//导航上显示的页数范围长度
	
		if($this->currentPage>1){
			$pre=$this->currentPage-1;
		}
		if($this->currentPage+1>$this->totalPage){
			$next= $this->totalPage;
		}
	
		if($this->currentPage>2){
			$begin=$this->currentPage-2;
		}
		if($this->currentPage>2){
			$end=$this->currentPage+2;
		}
	
		$str='<div id="pagelist" class="page">';
		$page_first=str_replace('{$page}',$firstPage,$path).$this->ext;
		$page_pre=str_replace('{$page}',$pre,$path).$this->ext;
		
		$page_next=str_replace('{$page}',$next,$path).$this->ext;
		$page_last=str_replace('{$page}',$this->totalPage,$path).$this->ext;
		//echo $page_next;
		//$page_pre=$path.$pre.$this->ext;
		
		//$page_next=$path.$next.$this->ext;
		//$page_last=$path.$this->totalPage.$this->ext;
			
		
		$str.= '<span class="left"><a href="'.$page_first.'">首页</a>';
		$str.= '<a href="'.$page_pre.'">上一页</a></span>';
		
		$str.='<span class="main">'.$this->currentPage.'/'.$this->totalPage.'</span>';
/*		for($i=$begin;$i<$end;$i++){
			if($i>$this->totalPage){
				$str.="<span style='color:#ccc'>$i</span>";
			}else{
				if($i==$this->currentPage){
					$str.="<span><span  class='onpage'>$this->currentPage</span></span>";
				}else{
				$page_mid=$path.$i.$this->ext;			
			   $str.='<span><a href="'.$page_mid.'">'.$i.'</a></span>';
				}
			}
		}//for*/
		
		
		$str.= '<span class="right"><a href="'.$page_next.'">下一页</a>';
		$str.= '<a href="'.$page_last.'">尾页</a></span>';
		
	
		$str.='</div>';
		return $str;
	}

//////////////

	public function showPage(){
	
		$pre=1;
		$next=$this->currentPage+1;
		$path=$this->subUrl;
		$var="'";
		$firstPage="1";		
		$begin=1;
		$end=6;//导航上显示的页数范围长度
	
		if($this->currentPage>1){
			$pre=$this->currentPage-1;
		}
		if($this->currentPage+1>$this->totalPage){
			$next= $this->totalPage;
		}
	
		if($this->currentPage>2){
			$begin=$this->currentPage-2;
		}
		if($this->currentPage>2){
			$end=$this->currentPage+2;
		}
	
		$str='<div id="pagelist" class="page">';
	
		$page_first=$path.$firstPage.$this->ext;
		$page_pre=$path.$pre.$this->ext;
		
		$page_next=$path.$next.$this->ext;
		$page_last=$path.$this->totalPage.$this->ext;
			
		
		$str.= '<span class="left"><a href="'.$page_first.'">首页</a>';
		$str.= '<a href="'.$page_pre.'">上一页</a></span>';
		
		$str.='<span class="main">'.$this->currentPage.'/'.$this->totalPage.'</span>';
/*		for($i=$begin;$i<$end;$i++){
			if($i>$this->totalPage){
				$str.="<span style='color:#ccc'>$i</span>";
			}else{
				if($i==$this->currentPage){
					$str.="<span><span  class='onpage'>$this->currentPage</span></span>";
				}else{
				$page_mid=$path.$i.$this->ext;			
			   $str.='<span><a href="'.$page_mid.'">'.$i.'</a></span>';
				}
			}
		}//for*/
		
		
		$str.= '<span class="right"><a href="'.$page_next.'">下一页</a>';
		$str.= '<a href="'.$page_last.'">尾页</a></span>';
		
	
		$str.='</div>';
		return $str;
	}
/////////////////////////////////////
}
?>
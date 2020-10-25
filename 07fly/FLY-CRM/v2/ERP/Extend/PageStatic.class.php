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

class PageStatic{
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
			
			$this->htmlPage=$this->showPage();
		
		}//else
		return $this->htmlPage;
	
	}
/////////////


//////////////


/////////////////////////////////////
}
?>